<?php

namespace App\Controller;

use App\Entity\User;
use App\LocationIPService;
use App\RefreshableDataService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $clientIp = $locatingService->getUsersIPAdress($request);
        // API keys for future work.(Not sure if need to hold in service)
        $getIPApiKey = $this->getParameter('IP_API_KEY');
        $getWeatherApiKey = $this->getParameter('WEATHERFORECAST_API_KEY');
        // Checking for alredy created user.
            $existedIPUser = $this->userRepository->findIp($clientIp);

                if ($existedIPUser == false) {
                    $clientLocation = $locatingService->getUsersLocation($clientIp,$getIPApiKey,$client);
                    $clientWeather = $locatingService->getUsersWeather($clientLocation->latitude,$clientLocation->longitude,$getWeatherApiKey,$client);

                        if ((!$clientLocation || !$clientWeather)) {
                            $this->createNotFoundException('We got some problems,with weather services. Please try again later.');
                        }
                            $user = new User();
                            $newUser = $user->saveUserDatas($clientIp,$clientLocation->latitude,$clientLocation->longitude,$clientLocation->city);
                            $this->userRepository->save($newUser);
                }
                else {
                    $refreshableStatus = $refresh->checkRefreshableStatus($request,$clientIp);
                    if ($refreshableStatus == true) {
                        // Getting current user,to edit. Since we fetching new datas.
                        $userLocation = $locatingService->getUsersLocation($clientIp, $getIPApiKey, $client);
                        $this->userRepository->refreshUser($existedIPUser, $clientIp, $userLocation->city, $userLocation->latitude, $userLocation->longitude);
                    }
                    $clientWeather = $locatingService->getUsersWeather($userLocation->latitude ?? $existedIPUser->latitude,$userLocation->longitude ?? $existedIPUser->longitude,$getWeatherApiKey,$client);
                }

        return $this->json([
            'City' => $clientWeather->name,
            'Clouds' => $clientWeather->weather[0],
            'Wind_Speed' => $clientWeather->wind,
            'Temperature' => $clientWeather->main,
        ]);
    }
}
