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

        // Test Access Token
        $fbAccessToken = "EAAXJ9LWpb0ABAMVZAAavRRZBVjl7nQK3sdJgJoA7oknSwoKhSRKfv94Rj2uTZB5V16PRTddSAfq9TmbaK1zxYc1wkobZCf0CfxMUNqoYEWbiiujoBmZB7GqdqqZBxpHApUZASuZBDZCtuOGpCi2jFZB6QQgxZCYdhsZByoRbwtqBwEtpYqO01S5sNxq4Co6s6bzSxrAxMnxFm6DK2DZBa5Ja6BTKkOHGkHN9MjFp6sIM3jwqwMOimib3REWxl";
        $session->set("fb_access_token", $fbAccessToken);

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

    /**
     * @param User $facebookUser
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userTest(User $facebookUser)
    {
        $user = $facebookUser->getMe();

        if (!empty($user)) {
            $user = $user->asArray();
        }

        return $this->render('facebook_auth/userTest.html.twig', [
            'user' => $user
        ]);
    }
}
