<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Category;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class ProfilController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profil")
     */
    public function index(OrderRepository $orderRepository ,EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);
        $orders = $orderRepository->findBy(['user_id' => $user]);


        if (!$user){
            return  $this->redirectToRoute('app_home');
        } else {
            $adresses = $em->getRepository(Adresses::class)->findBy(['user' => $user]);
            $form = $this->createForm(ChangePasswordFormType::class);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $oldPassword = $data['oldPassword'];
                $newPassword = $data['newPassword'];

                if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                    $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($encodedPassword);
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
                    return $this->redirectToRoute('app_profil');
                } else {
                    $this->addFlash('error', 'Ancien mot de passe incorrect.');
                }
            }
            
            return $this->render('profil/profil.html.twig', [
                'controller_name' => 'ProfilController',
                'peluchesCategory' => $peluchesCategory,
                'doudousCategory' => $doudousCategory,
                'user' => $user,
                'orders' => $orders,
                'adresses' => $adresses,
                'formulaire' => $form->createView(),
            ]);
        }
    }
    /**
     * @Route("/profile/update/user", name="profil_user_update")
     */
    public function updateUser(EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        $adresses = $em->getRepository(Adresses::class)->findBy(['user' => $user]);
        $user_firstname = $request->request->get('user-firstname');
        $user_lastname = $request->request->get('user-lastname');
        $user_email = $request->request->get('user-email');
        $user_mobile = $request->request->get('user-mobile');
        
        // $user = new User();
        $user->setFirstname($user_firstname);
        $user->setLastname($user_lastname);
        // $user->setEmail($user_email);
        $user->setMobile($user_mobile);
        
        // dd($user);
        // $em->persist($user);
        $em->flush();

        // dd($user);
        return $this->redirectToRoute('app_profil');
    }

    /**
     * @Route("/profile/update/password", name="profil_user_update_password")
     */
    public function updatePassword(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $user_old_password = $request->request->get('older-password');
        $user_new_password = $request->request->get('new-password');
        $user_renew_password = $request->request->get('re-new-password');

        
        if ($passwordEncoder->isPasswordValid($user, $user_old_password))
        {
            if ($user_new_password === $user_renew_password)
            {
                // Encode (hash) the plain password, and set it.
                $encodedPassword = $passwordEncoder->hashPassword($user, $user_new_password);

                $user->setPassword($encodedPassword);
                $em->flush();

                return $this->redirectToRoute('app_profil');
            }
        }
        return $this->redirectToRoute('app_profil');
    }

    /**
     * @Route("/profile/order/invoice/{id}", name="app_order_invoice")
     */
    public function invoice($id,InvoiceController $invoice){

        // $user = $this->getUser();
        // $order = $orderRepository->find($id);
        $invoice->generateInvoice($id);
    }
}
