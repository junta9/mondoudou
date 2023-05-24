<?php

namespace App\Controller;

use App\Service\CartService;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PayementController extends AbstractController
{
    /**
     * @Route("/payement", name="app_payement")
     */
    public function payement(CartService $cartService)
    {
        // clé secrete pour que stripe me reconnaisse
        $stripeSecretKey="sk_test_51N7BLTLSTvAVXmvAMIVodTwS5yNjyYebx3cdcXYH9CGn40AcMwpVRNSgi30nbQ1aYIdM4IKtM9QOzUn22V6RlI0v00pxiDAkKs";
        Stripe::setApiKey($stripeSecretKey);
        
        // on définit le protocol de connexion http ou https
        // avec les variable global PHP de sorte de pouvoir gérer
        // tout les environnements possible
        
        if (isset($_SERVER['HTTPS'])){
            $protocol="https://";
        } 
        $protocol="http://";
        // on définit le nom du serveur de connexion 
        // avec les variable global PHP de sorte de pouvoir gérer
        // tout les environnements possible
        $serveur=$_SERVER['SERVER_NAME'];
        $YOUR_DOMAIN=$protocol.$serveur;

        $transporteur = $this->get('session')->get('transporteur', 0)*100;

        $montant=$cartService->getTotalPrice()*100;
        //Je dois créer une variable pour recup le transporteur
        // on lance la communication avec stripe
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
              # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
              'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $montant + $transporteur,
                'product_data' => [
                  'name' => 'Paiement de votre panier'
                ],
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/profile/commande/success',
            'cancel_url' => $YOUR_DOMAIN . '/profile/commande/cancel',

        ]);

        
        return $this->render('commande/index.html.twig', [
            'id_session'=>$checkout_session->id
        ]);
    }
}
