<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(CartService $cartService)
    {
        $panierWithDatas = $cartService->show();
        $total = $cartService->getTotalPrice();
        $totalQuantity = $cartService->getTotalQuantity();

        return $this->render('panier/panier.html.twig', [
            'controller_name' => 'HomeController',
            'items' => $panierWithDatas,
            'total' => $total,
            'totalQuantity' => $totalQuantity,
        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="add_panier")
     */
    public function add($id, CartService $cartService, Request $request)
    {
        $cartService->add($id);
        $totalQuantity = $cartService->getTotalQuantity();

        if ($request->isXmlHttpRequest())
        {
            return $this->json([
                'success' => true,
                'totalQuantity' => $totalQuantity,
            ]);
        }
        return $this->redirectToRoute("app_panier");
    }

    /**
     * @Route("/panier/remove/{id}", name="del_panier")
     */
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);

        return $this->redirectToRoute("app_panier");
    }
    
    /**
     * @Route("/panier/remove", name="del_panier_all")
     */
    public function removeAll(CartService $cartService)
    {
        $cartService->removeAll();

        return $this->redirectToRoute("app_panier");
    }

}
