<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use App\Repository\AdressesRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Cast\Bool_;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
    $stripeSecretKey = "sk_test_51N7BLTLSTvAVXmvAMIVodTwS5yNjyYebx3cdcXYH9CGn40AcMwpVRNSgi30nbQ1aYIdM4IKtM9QOzUn22V6RlI0v00pxiDAkKs";
    Stripe::setApiKey($stripeSecretKey);

    // on définit le protocol de connexion http ou https
    // avec les variable global PHP de sorte de pouvoir gérer
    // tout les environnements possible

    if (isset($_SERVER['HTTPS']))
    {
      $protocol = "https://";
    }
    $protocol = "http://";
    // on définit le nom du serveur de connexion 
    // avec les variable global PHP de sorte de pouvoir gérer
    // tout les environnements possible
    $serveur = $_SERVER['SERVER_NAME'];
    $YOUR_DOMAIN = $protocol . $serveur;

    $transporteur = $this->get('session')->get('transporteur', 0) * 100;

    $montant = $cartService->getTotalPrice() * 100;
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
      'id_session' => $checkout_session->id
    ]);
  }
  /**
   * @Route("/profile/commande/success", name="app_commande_sucess")
   */
  public function success(
    RequestStack $session,
    ProductRepository $productRepository,
    CartService $cartService,
    EntityManagerInterface $em
  ): Response
  {
    $panier = $session->getSession()->get("panier");
    $adresseDelivery = $session->getSession()->get("adresseDelivery");
    $transporteur = $session->getSession()->get('transporteur');

    // création d'un objet order
    $order = new Order();
    $order->setUserId($this->getUser());
    $order->setStatus('En attente'); // Set the initial status of the order
    $order->setCreatedAt(new \DateTimeImmutable());
    $order->setDeliveryPrice($transporteur);
    $order->setDeliveryAddress($adresseDelivery);

    foreach ($panier as $key => $quantity)
    {
      $product = $productRepository->find($key);

      // Create a new OrderItem entity for each item
      $orderItem = new OrderItem();
      $orderItem->setOrderId($order);
      $orderItem->setProduct($product);
      $orderItem->setQuantity($quantity);
      $orderItem->setPrice($product->getPrice()); // Assuming the price is stored in the Product entity
      $totalPrice = $orderItem->getPrice() * $orderItem->getQuantity();
      $orderItem->setTotalPrice($totalPrice);
      $order->addOrderItem($orderItem); // Add the OrderItem to the Order entity
      $order->setTotal($cartService->getTotalPrice() + $transporteur);
      
      
    }
    // Create payment object
    $payment = new Payment();
    $payment->setOrderId($order);
    $payment->setTotal($order->getTotal());
    $payment->setCreatedAt(new \DateTimeImmutable());
    
    // Persist the Order entity and its associated OrderItems
    $em->persist($order);
    $em->persist($payment);
    $em->flush();
    
    // remove session variables
    $cartService->removeAll();
    $session->getSession()->remove('transporteur');
    $session->getSession()->remove('adresseDelivery');

    if (isset($_SERVER['HTTPS']))
    {
      $protocol = "https://";
    }
    $protocol = "http://";
    // on définit le nom du serveur de connexion 
    // avec les variable global PHP de sorte de pouvoir gérer
    // tout les environnements possible
    $serveur = $_SERVER['SERVER_NAME'];
    $YOUR_DOMAIN = $protocol . $serveur;
    $profil = $YOUR_DOMAIN . '/profile';

    $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
    $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);

    return $this->render(
      "commande/success.html.twig", [
        'profil' => $profil,
        'peluchesCategory' => $peluchesCategory,
        'doudousCategory' => $doudousCategory,
      ]
    );
  }
}
