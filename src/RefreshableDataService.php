<?php

namespace App;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class RefreshableDataService extends UserRepository
{
    public function checkRefreshableStatus(Request $request,string $ip_adress): bool
    {
        $getRequestData = json_decode($request->getContent(), false);
        $isRefreshable = $getRequestData->refresh ?? false;
            if ($isRefreshable == true) {
                return true;
            }
            return false;
    }
}
