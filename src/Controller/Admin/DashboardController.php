<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Messages;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Photos;
use App\Entity\Product;
use App\Entity\Transporteur;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Entity\File;

class DashboardController extends AbstractDashboardController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {   
        return $this->statistics();
        // return parent::index();
        // redirect to some CRUD controller
        // $routeBuilder = $this->get(AdminUrlGenerator::class);

        // return $this->redirect($routeBuilder->setController(ProductCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mondoudou');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fa fa-truck', Transporteur::class);
        yield MenuItem::linkToCrud('Produits', 'fab fa-product-hunt', Product::class);
        yield MenuItem::linkToCrud('Photos', 'fa fa-image', Photos::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Order::class);
        yield MenuItem::linkToCrud('Commandes produits', 'fa fa-cart-plus', OrderItem::class);
        yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Messages', 'fa fa-envelope-open-text', Messages::class);
        yield MenuItem::linkToRoute('Retour sur le site', 'fa fa-home', 'app_home');
    }

    public function statistics(){

        $users = $this->entityManager->getRepository(User::class)->findAll();
        $orders = $this->entityManager->getRepository(Order::class)->findAll();
        // $ales = $this->entityManager->getRepository('order')->find;

        return $this->render('admin/dashboard.html.twig', [
            'users' => count($users),
            'orders' => count($orders),
        ]);
    }
}
