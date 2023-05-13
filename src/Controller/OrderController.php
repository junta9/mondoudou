<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Form\OrderType;
use App\Repository\AdressesRepository;
use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/create", name="order_index")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository, PhotosRepository $photosRepository, EntityManagerInterface $em): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        $user = $this->getUser();
        $adresses = $em->getRepository(Adresses::class)->findBy(['user' => $user]);

        $panier = $session->get('panier', []);
        $form = $this->createForm(OrderType::class, null, [
            'user' => $user,
        ]);

        $panierWithDatas = [];
        foreach($panier as $id => $quantity) 
        {
            $panierWithDatas[] = [
                'product' => $productRepository->find($id),
                'photo' => $photosRepository->findOneBy(['product' => $id]),
                'quantity' => $quantity,
            ];
            
        }
        $total = 0;
        $totalQuantity = 0;
        // $shipping = 5;

        foreach($panierWithDatas as $item)
        {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
            $totalQuantity += $item['quantity'];
        }

        // $totalInclShipping = $total + $shipping;


        // var_dump($adresse);
        // die();
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form->createView(),
            'items' => $panierWithDatas,
            'total' => $total,
            // 'totalInclShipping' => $totalInclShipping,
            'totalQuantity' => $totalQuantity,
            'user' => $user,
            'adresses' => $adresses,
        ]);
    }
}
