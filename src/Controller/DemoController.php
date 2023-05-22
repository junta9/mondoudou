<?php

namespace App\Controller;

use App\Service\AfficheToto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{
    /**
     * @Route("/demo", name="app_demo")
     */
    public function index(AfficheToto $afficheToto): Response
    {
        // autowiring : instanciation de la classe plus haut.
        // injection de dépendance.
        $afficheToto->afficheToto();

        return $this->render('demo/index.html.twig', [
            'controller_name' => 'DemoController',
        ]);
    }
}
