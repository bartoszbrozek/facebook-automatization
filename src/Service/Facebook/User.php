<?php

namespace App\Service\Facebook;

use App\Service\FacebookService;
use Facebook\Exceptions\FacebookSDKException;

class User extends FacebookService
{
    public function getMe(): ?\Facebook\GraphNodes\GraphUser
    {
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->getFb()
                ->get('/me?fields=id,name', $this->getFbAccessToken());

            return $response->getGraphUser();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $this->session->getFlashBag()
                ->add("error", "Graph Error: " . $e->getMessage());
        } catch (FacebookSDKException $e) {
            $this->session->getFlashBag()
                ->add("error", "SDK Error: " . $e->getMessage());
        }

        return null;
    }
}
