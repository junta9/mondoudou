<?php

namespace App\Controller;

use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="app_panier")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository, PhotosRepository $photosRepository)
    {
        $panier = $session->get('panier', []);
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
        $shipping = 5;

        foreach($panierWithDatas as $item)
        {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        $totalInclShipping = $total + $shipping;
        return $this->render('panier/panier.html.twig', [
            'controller_name' => 'HomeController',
            'items' => $panierWithDatas,
            'total' => $total,
            'totalInclShipping' => $totalInclShipping,
        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="add_panier")
     */
    public function add($id, SessionInterface $session, RequestStack $requestStack, RouterInterface $router, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);
        $product = $productRepository->find($id);
        // $currentPath = $requestStack->getMainRequest()->getBaseUrlReal();
        // $url = $router->generate($currentPath);
        
        if(!empty($panier[$id])) 
        {
            if($panier[$id] < $product->getQuantity())
            {
                $panier[$id]++;
            } 
            // else {
            //     $this->addFlash('error', "Vous ne pouvez pas ajouter au dela du stock disponible");
            // }
        } else {
            $panier[$id] = 1;
            // dd($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute("app_panier");
        // return new RedirectResponse($url);
    }

    /**
     * @Route("/panier/remove/{id}", name="del_panier")
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id]))
        {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }
    
    // return $this->redirectToRoute('app_panier');

}
