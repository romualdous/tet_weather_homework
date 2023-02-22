<?php

namespace App\Controller;

use App\Entity\User;
use App\LocationIPService;
use App\RefreshableDataService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Sodium\add;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class WeatherController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    #[Route('/weather', name: 'app_weather')]
    public function getWeather(Request $request, HttpClientInterface $client, LocationIPService $locatingService, RefreshableDataService $refresh)
    {
        //starting cache
        $cache = new FilesystemAdapter();
        $clientIp = $locatingService->getUsersIPAdress($request);

        // API keys for future work.(Not sure if need to hold in service)
        $getIPApiKey = $this->getParameter('IP_API_KEY');
        $getWeatherApiKey = $this->getParameter('WEATHERFORECAST_API_KEY');#
        //If we have data in cashe,and anyway whant to update datas ,then need to clear them before if's statements
        $refreshableStatus = $refresh->checkRefreshableStatus($request);
        if($refreshableStatus == true) {
            $cache->clear();
        }
        //Getting cached datas
        $cachedUser = $cache->getItem('ip_adress');
        if (!$cachedUser->isHit()) {
            // Checking for alredy created user in DB to fetch datas from there.
            $existedIPUser = $this->userRepository->findIp($clientIp);
            if ($existedIPUser == false) {
                $clientLocation = $locatingService->getUsersLocation($clientIp, $getIPApiKey, $client);
                if($clientLocation->latitude == null || $clientLocation->longitude == null) {
                    return $this->json([
                        'message' => 'We couldnt get your location from webservice,please try again later.'
                    ], 400);
                }
                $clientWeather = $locatingService->getUsersWeather($clientLocation->latitude, $clientLocation->longitude, $getWeatherApiKey, $client);

                if ((!$clientLocation || !$clientWeather)) {
                    return $this->json([
                        'message' => 'We got some problems,with weather services. Please try again later.'
                    ], 400);
                }
                $user = new User();
                $newUser = $user->saveUserDatas($clientIp, $clientLocation->latitude, $clientLocation->longitude, $clientLocation->city);
                $this->userRepository->save($newUser);
            } else {
                if ($refreshableStatus == true) {
                    // Getting current user,to edit. Since we fetching new datas.
                    $userLocation = $locatingService->getUsersLocation($clientIp, $getIPApiKey, $client);
                    $this->userRepository->refreshUser($existedIPUser, $clientIp, $userLocation->city, $userLocation->latitude, $userLocation->longitude);
                }
                $clientWeather = $locatingService->getUsersWeather($userLocation->latitude ?? $existedIPUser->latitude, $userLocation->longitude ?? $existedIPUser->longitude, $getWeatherApiKey, $client);
            }
            //Saving new datas to cache
            $cachedUser->expiresAfter(360);
            $cache->save($cachedUser->set(['ip' => $clientIp, 'weather_data' => $clientWeather]));
        }
        else
        {
            $userInMemory = $cachedUser->get();
            $clientWeather = $userInMemory['weather_data'];
        }


        return $this->json([
            'City' => $clientWeather->name,
            'Clouds' => $clientWeather->weather[0],
            'Wind_Speed' => $clientWeather->wind,
            'Temperature' => $clientWeather->main
        ]);
    }
}
