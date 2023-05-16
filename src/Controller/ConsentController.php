<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsentController extends AbstractController
{
    /**
     * @Route("/consent", name="app_consent")
     */
    public function showBanner(): Response
    {
        return $this->render('consent_banner.html.twig', [
            'controller_name' => 'ConsentController',
        ]);
    }
}
