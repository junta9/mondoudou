<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Category;
use App\Entity\Transporteur;
use App\Entity\User;
use App\Form\OrderType;
use App\Repository\AdressesRepository;
use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
use App\Repository\TransporteurRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/view", name="order_index")
     */
    public function index(CartService $cartService, AdressesRepository $adressesRepository, TransporteurRepository $transporteurRepository): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }
        $user = $this->getUser();
        $adresses = $adressesRepository->findBy(['user' => $user]);
        $form = $this->createForm(OrderType::class, null, [
            'user' => $user,
        ]);

        $panierWithDatas = $cartService->show();
        $total = $cartService->getTotalPrice();
        $totalQuantity = $cartService->getTotalQuantity();

        $deliverys = $transporteurRepository->findAll();
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView(),
            'items' => $panierWithDatas,
            'total' => $total,
            // 'totalInclShipping' => $totalInclShipping,
            'totalQuantity' => $totalQuantity,
            'user' => $user,
            'adresses' => $adresses,
            'deliverys' => $deliverys,
            
        ]);
    }

    /**
     * @Route("/order/total", name="order_total")
     */
    public function getTotalPriceIncDelivery() {
        // $totalInclShipping =
        dd();
    }

    /**
     * @Route("/order/create", name="order_create")
     */
    public function create(CartService $cartService)
    {

        $montant=$cartService->getTotalPrice()*100;

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

        // on lance la communication avec stripe
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
              # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
              'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $montant,
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
