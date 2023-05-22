<?php 


namespace App\Service;

use App\Repository\ProductRepository;

class AfficheToto{
    private $repodeproduits;

    function __construct(ProductRepository $productRepository){
        $this->repodeproduits = $productRepository;
    }

    public function afficheToto(){
        dd($this->repodeproduits->findAll());
        // $toto = 'Ma variable';
        // dd($toto);
    }
}