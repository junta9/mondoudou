<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Category;
use App\Entity\Transporteur;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\OrderType;
use App\Form\TransporteurType;
use App\Repository\AdressesRepository;
use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
use App\Repository\TransporteurRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class OrderController extends AbstractController
{

    private $router;
    private $session;
    private $requestStack;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->router = $router;
        // $this->session = $session;
        $this->requestStack = $requestStack;
    }
    /**
     * @Route("/order/view", name="order_index")
     */
    public function index(SessionInterface $session,CartService $cartService, AdressesRepository $adressesRepository, TransporteurRepository $transporteurRepository, Request $request): Response
    {

        //if cart is empty, redirect to home page
        if(empty($cartService->show())){
            return $this->redirectToRoute('app_home');
        }

        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        //Recuperation des données pour creation de la commande
        $user = $this->getUser();
        $adresses = $adressesRepository->findBy(['user' => $user]);

        //If address found, it wil store the first address in the session
        if($adresses)
        {
            $session = $request->getSession();
            $session->set('adresseDelivery', $adresses[0]->getId());
        }
        $adresseDelivery = $session->get('adresseDelivery');
        
        $form = $this->createForm(OrderType::class, null, [
            'user' => $user,
        ]);
        $transportForm = $this->createForm(TransporteurType::class);
        $transportForm->handleRequest($request);
        $transporteur = 5;
        $this->get('session')->set('transporteur', $transporteur);

        if ($transportForm->isSubmitted() && $transportForm->isValid()) {
            $transporteur = $transportForm->get('transporteur')->getData();
            $this->get('session')->set('transporteur', $transporteur);

        }
        
        $panierWithDatas = $cartService->show();
        $total = $cartService->getTotalPrice();
        $totalInclShipping = $total + $transporteur;

        $totalQuantity = $cartService->getTotalQuantity();

        $deliverys = $transporteurRepository->findAll();
        $adresse = new Adresses();
        $formAdresse = $this->createForm(AdresseType::class, $adresse);

        if($formAdresse->isSubmitted() && $formAdresse->isValid())
        {
            // Store the current path in the session
            $currentRoute = $this->router->match($this->requestStack->getCurrentRequest()->getPathInfo())['_route'];
            $session->set('current_route', $currentRoute);
        }

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView(),
            'transportForm' => $transportForm->createView(),
            'items' => $panierWithDatas,
            'total' => $total,
            'totalInclShipping' => $totalInclShipping,
            'totalQuantity' => $totalQuantity,
            'user' => $user,
            'adresses' => $adresses,
            'deliverys' => $deliverys,
            'formAdresse' => $formAdresse->createView(),
            
        ]);
    }

    /**
     * @Route("/order/delivery", name="order_delivery")
     */
    public function setDelivery(Request $request)
    {
        // Récupérer l'identifiant de l'adresse sélectionnée
        $adresseDelivery = $request->request->get('adresse_id');

        // Enregistrer l'identifiant de l'adresse dans la session
        $session = $request->getSession();
        $session->set('adresseDelivery', $adresseDelivery);
        // dd($adresseDelivery);

        // Autres traitements de votre contrôleur...

        // Redirection vers une autre page
        return $this->redirectToRoute('order_index');
    }
}
