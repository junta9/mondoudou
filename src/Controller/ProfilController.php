<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Category;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\ChangePasswordFormType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class ProfilController extends AbstractController
{

    private $router;
    private $session;
    private $requestStack;

    public function __construct(RequestStack $requestStack, RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/profile", name="app_profil")
     */
    public function index(OrderRepository $orderRepository, UserRepository $userRepository, EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->find($this->getUser());
        $adresse = new Adresses();
        $formAdresse = $this->createForm(AdresseType::class, $adresse);
        $orders = $orderRepository->findBy(['user_id' => $user]);




        if (!$user)
        {
            return  $this->redirectToRoute('app_home');
        }
        else
        {
            $adresses = $em->getRepository(Adresses::class)->findBy(['user' => $user]);

            $form = $this->createForm(ChangePasswordFormType::class);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $plainPassword = $form->get('plainPassword')->getData();
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                if($user->getPassword() === $encodedPassword)
                {
                    $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');

                } else {

                    $this->addFlash('error', 'Les modifications ont échoué, veuillez recommencer.');
                }
            } elseif ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash('error', 'Les modifications ont échoué, veuillez recommencer.');
                
            }

            if($formAdresse->isSubmitted() && $formAdresse->isValid())
            {
                // Store the current path in the session
                $currentRoute = $this->router->match($this->requestStack->getCurrentRequest()->getPathInfo())['_route'];
                $this->session->set('current_route', $currentRoute);
            }
            $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
            $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);
            
            return $this->render('profil/profil.html.twig', [
                'controller_name' => 'ProfilController',
                'user' => $user,
                'adresses' => $adresses,
                'formulaire' => $form->createView(),
                'formAdresse' => $formAdresse->createView(),
                'peluchesCategory' => $peluchesCategory,
                'doudousCategory' => $doudousCategory,
                'orders' => $orders,
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
        // $user_email = $request->request->get('user-email');
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
        $this->addFlash('success', 'Vos informations ont été mis à jour avec succès.');

        return $this->redirectToRoute('app_profil');
    }

    /**
     * @Route("/profile/update/password", name="profil_user_update_password")
     */
    // public function updatePassword(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordEncoder)
    // {
    //     $user = $this->getUser();
    //     $user_old_password = $request->request->get('older-password');
    //     $user_new_password = $request->request->get('new-password');
    //     $user_renew_password = $request->request->get('re-new-password');


    //     if ($passwordEncoder->isPasswordValid($user, $user_old_password))
    //     {
    //         if ($user_new_password === $user_renew_password)
    //         {
    //             // Encode (hash) the plain password, and set it.
    //             $encodedPassword = $passwordEncoder->hashPassword($user, $user_new_password);

    //             $user->setPassword($encodedPassword);
    //             $em->flush();

    //             return $this->redirectToRoute('app_profil');
    //         }
    //     }
    //     return $this->redirectToRoute('app_profil');
    // }

    /**
     * @Route("/profile/order/invoice/{id}", name="app_order_invoice")
     */
    public function invoice($id, InvoiceController $invoice)
    {

        $invoice->generateInvoice($id);
    }
}
