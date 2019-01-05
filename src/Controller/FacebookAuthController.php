<?php

namespace App\Controller;

use App\Service\Facebook\User;
use App\Service\FacebookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class FacebookAuthController extends AbstractController
{
    public function index(FacebookService $facebookService, Session $session)
    {
        $loginUrl = $facebookService->getLoginUrl();

        if ($session->get("fb_access_token")) {
            return $this->redirectToRoute("facebook_usertest");
        }

        return $this->render('facebook_auth/index.html.twig', [
            'loginUrl' => $loginUrl
        ]);
    }

    public function authorize(FacebookService $facebookService, Session $session)
    {
        $fbAccessToken = $facebookService->getFbAccessToken();

        $session->set("fb_access_token", $fbAccessToken);

        return $this->redirectToRoute("facebook_auth");
    }

    public function userTest(User $user)
    {
        return $this->render('facebook_auth/userTest.html.twig', [
            'user' => $user->getMe()->asArray()
        ]);
    }
}
