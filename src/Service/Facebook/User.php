<?php

namespace App\Service\Facebook;

use App\Service\FacebookService;
use Facebook\Exceptions\FacebookSDKException;

class User extends FacebookService
{
    public function getMe()
    {
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->getFb()
                ->get('/me?fields=id,name', $this->getFbAccessToken());

            return $response->getGraphUser();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            dump($e);
            echo '1Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            dump($e);
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

    }
}
