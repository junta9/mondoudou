<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Form\AdresseType;
use App\Repository\AdressesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{
    /**
     * @Route("/adresse", name="adresse_index")
     */
    public function index(): Response
    {
        return $this->render('adresse/index.html.twig', [
            'controller_name' => 'AdresseController',
        ]);
    }

    /**
     * @Route("/adresse/new", name="adresse_new")
     */
    public function new(Request $request, EntityManagerInterface $em)
    {
        $adresse = new Adresses();
        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            // Traitement de l'adresse ici, comme l'enregistrement dans la base de données
            $user = $this->getUser();
            $adresse->setUser($user);
            $em->persist($adresse);
            $em->flush();
            return $this->redirectToRoute('app_panier');

        }

        return $this->render('adresse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/adresse/del/{id}", name="adresse_del")
     */
    public function del($id, AdressesRepository $adressesRepository, EntityManagerInterface $em)
    {
        //suppression de l'adresse dans la base de données
        $adresse = $adressesRepository->find($id);
        $adressesRepository->remove($adresse);
        $em->flush();
        return $this->redirectToRoute('app_profil');
    }
}
