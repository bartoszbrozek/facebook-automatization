<?php

namespace App\Controller;

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
}
