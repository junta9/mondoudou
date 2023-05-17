<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
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
    public function index(SessionInterface $session, ProductRepository $productRepository, PhotosRepository $photosRepository, EntityManagerInterface $em)
    {
        $panier = $session->get('panier', []);
        $panierWithDatas = [];
        foreach ($panier as $id => $quantity)
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

        foreach ($panierWithDatas as $item)
        {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
            $totalQuantity += $item['quantity'];
        }

        // $totalInclShipping = $total + $shipping;

        $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);
        return $this->render('panier/panier.html.twig', [
            'controller_name' => 'HomeController',
            'items' => $panierWithDatas,
            'total' => $total,
            // 'totalInclShipping' => $totalInclShipping,
            'totalQuantity' => $totalQuantity,
            'peluchesCategory' => $peluchesCategory,
            'doudousCategory' => $doudousCategory,
        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="add_panier")
     */
    public function add($id, SessionInterface $session, ProductRepository $productRepository, Request $request)
    {
        $panier = $session->get('panier', []);
        $product = $productRepository->find($id);

        if (!empty($panier[$id]))
        {
            if ($panier[$id] < $product->getQuantity())
            {
                $panier[$id]++;
            }
        }
        else
        {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);
        $panierWithDatas = [];
        foreach ($panier as $id => $quantity)
        {
            $panierWithDatas[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity,
            ];
        }
        $total = 0;
        $totalQuantity = 0;
        // $shipping = 5;

        foreach ($panierWithDatas as $item)
        {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
            $totalQuantity += $item['quantity'];
        }
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
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id]))
        {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }
    
    /**
     * @Route("/panier/remove", name="del_panier_all")
     */
    public function removeAll(SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier))
        {
            $panier = [];
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");
    }

}
