<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LocationIPService
{
    public function getUsersIPAdress(Request $request): string
    {
        $request->getClientIp();
        return $request->getClientIp();
    }

    public function getUsersLocation(string $ip, string $api_key, HttpClientInterface $client)
    {
        $ipLocation = $client->request('POST', 'http://api.ipstack.com/89.254.150.224?access_key=' . $api_key);
        $ipLocationData = $ipLocation->getContent();

        return json_decode($ipLocationData);
    }

    public function getUsersWeather(string $latitude,string $longitude, string $api_key, HttpClientInterface $client)
    {
        $weatherStatus = $client->request('POST', 'https://api.openweathermap.org/data/2.5/weather?lat='.$latitude.'&lon='.$longitude.'&appid='.$api_key);
        return json_decode($weatherStatus->getContent());
    }
}
