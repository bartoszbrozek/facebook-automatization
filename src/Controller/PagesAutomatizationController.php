<?php

namespace App\Controller;

use App\Service\Facebook\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PagesAutomatizationController extends AbstractController
{
    public function index()
    {
        return $this->render('pages_automatization/index.html.twig', [
            'controller_name' => 'PagesAutomatizationController',
        ]);
    }

    public function test()
    {
        return $this->render('pages_automatization/test.html.twig', [

        ]);
    }

    public function createTestPage(Page $page)
    {
        $testPage = $page->createTest();

        return $this->render('pages_automatization/test.html.twig', [
            'responseData' => $testPage
        ]);
    }


    public function createTestPost(Page $page)
    {
        $testPost = $page
            ->setPageId(602428060200389)
            ->createPost("Och jak ja kocham Kulfona, to jest ładnijeszy kto niż ja");

        return $this->render('pages_automatization/test.html.twig', [
            'responseData' => $testPost
        ]);
    }
}
