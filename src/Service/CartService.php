<?php 

namespace App\Service;

use App\Repository\PhotosRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    private $session;
    private $productRepository;
    private $photosRepository;

    public function __construct(
        SessionInterface $session,
        PhotosRepository $photosRepository,
        ProductRepository $produitRepository)
    {
        $this->session = $session;
        $this->productRepository = $produitRepository;
        $this->photosRepository = $photosRepository;
    }

    public function show(){
        $panier = $this->session->get('panier', []);
        $panierWithDatas = [];
        foreach ($panier as $id => $quantity)
        {
            $panierWithDatas[] = [
                'product' => $this->productRepository->find($id),
                'photo' => $this->photosRepository->findOneBy(['product' => $id]),
                'quantity' => $quantity,
            ];
        }
        return $panierWithDatas;
    }

    public function getTotalPrice(){
        $panierWithDatas = $this->show();
        $total = 0;

        foreach ($panierWithDatas as $item)
        {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $total;
    }

    public function getTotalQuantity(){
        $panierWithDatas = $this->show();
        $totalQuantity = 0;

        foreach ($panierWithDatas as $item)
        {
            $totalQuantity += $item['quantity'];
        }
        return $totalQuantity;

    }

    public function add($id)
    {
        $panier = $this->session->get('panier', []);
        $product = $this->productRepository->find($id);

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
        $this->session->set('panier', $panier);
    }

    public function remove($id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id]))
        {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    public function removeAll()
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier))
        {
            $panier = [];
        }
        $this->session->set('panier', $panier);
    }
}