<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/accept-cookies", name="accept_cookies")
     */
    public function acceptCookies(Request $request, Cookie $cookie)
    {
        // Récupérer les préférences de l'utilisateur (vous pouvez utiliser différentes méthodes pour les récupérer)

        $preferences = [
            'analytics' => true,
            'marketing' => true,
            'functional' => true,
        ];

        // Activer tous les types de cookies
        // Vous pouvez adapter cette partie en fonction de votre configuration et des types de cookies que vous utilisez

        if ($preferences['analytics'])
        {
            $analyticsCookie = new Cookie('analytics', 'enabled', time() + 86400 * 365, '/', null, false, false);
            $cookie->set($analyticsCookie);
        }

        if ($preferences['marketing'])
        {
            $marketingCookie = new Cookie('marketing', 'enabled', time() + 86400 * 365, '/', null, false, false);
            $cookie->set($marketingCookie);
        }

        if ($preferences['functional'])
        {
            $functionalCookie = new Cookie('functional', 'enabled', time() + 86400 * 365, '/', null, false, false);
            $cookie->set($functionalCookie);
        }

        // Appliquer les modifications de préférences
        // Vous pouvez ajouter ici d'autres actions dépendant des préférences de l'utilisateur

        // Redirection ou réponse JSON, selon vos besoins
        // Par exemple, vous pouvez rediriger l'utilisateur vers une page spécifique après avoir accepté les cookies
        // return $this->redirectToRoute('homepage');
    }

    
}
