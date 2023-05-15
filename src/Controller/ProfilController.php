<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="app_profil")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);

        $user = $this->getUser();
        $adresses = $em->getRepository(Adresses::class)->findBy(['user' => $user]);

        return $this->render('profil/profil.html.twig', [
            'controller_name' => 'ProfilController',
            'peluchesCategory' => $peluchesCategory,
            'doudousCategory' => $doudousCategory,
            'user' => $user,
            'adresses' => $adresses,
        ]);
    }
}
