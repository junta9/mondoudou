<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photos;
use App\Entity\Product;
use App\Repository\PhotosRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Photos::class);
        $peluchesPhotos = $repository->createQueryBuilder('p')
            ->where('p.imageName LIKE :keyword')
            ->setParameter('keyword', '%peluche%')
            ->getQuery()
            ->getResult();

        $photosPeluches = [];

        foreach ($peluchesPhotos as $photo) {
            $photosPeluches[] = [
                'id' => $photo->getId(),
                'imageName' => $photo->getImageName(),
                // ajoutez d'autres champs de l'entité Photo ici si nécessaire
            ];
        }
        $doudousPhotos = $repository->createQueryBuilder('p')
            ->where('p.imageName LIKE :keyword')
            ->setParameter('keyword', '%doudou%')
            ->getQuery()
            ->getResult();

        $photosDoudous = [];

        foreach ($doudousPhotos as $photo) {
            $photosDoudous[] = [
                'id' => $photo->getId(),
                'imageName' => $photo->getImageName(),
                // ajoutez d'autres champs de l'entité Photo ici si nécessaire
            ];
        }
        $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);
        $peluches = $em->getRepository(Product::class)->findBy(['category' => '2']);
        $doudous = $em->getRepository(Product::class)->findBy(['category' => '1']);
        $photos = $em->getRepository(Photos::class)->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'peluches' => $peluches,
            'doudous' => $doudous,
            'photos' => $photos,
            'peluchesPhotos' => $peluchesPhotos,
            'doudousPhotos' => $doudousPhotos,
            'peluchesCategory' => $peluchesCategory,
            'doudousCategory' => $doudousCategory,
        ]);
    }

    /**
     * @Route("product/{id}", name="app_product")
     */
    public function product($id, ManagerRegistry $doctrine, EntityManagerInterface $em)
    {
        $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);
        $photo = $doctrine->getRepository(Photos::class)->findOneBy(['product' => $id]);
        $product = $doctrine->getRepository(Product::class)->find($id);
        return $this->render("products/product.html.twig", [
            "product" => $product,
            'photo' => $photo,
            'peluchesCategory' => $peluchesCategory,
            'doudousCategory' => $doudousCategory,
        ]);
    }

    /**
     * @Route("category/{id}", name="app_category")
     */
    public function category(
        $id, 
        ManagerRegistry $doctrine
        ,EntityManagerInterface $em,
        PaginatorInterface $paginator,
        Request $request
        )
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        $categoryName = $category->getName();
        $repository = $em->getRepository(Photos::class);
        $peluchesPhotos = $repository->createQueryBuilder('p')
            ->where('p.imageName LIKE :keyword')
            ->setParameter('keyword', '%'.$categoryName.'%')
            ->getQuery()
            ->getResult();

        $photosPeluches = [];

        foreach ($peluchesPhotos as $photo) {
            $photosPeluches[] = [
                'id' => $photo->getId(),
                'imageName' => $photo->getImageName(),
                // ajoutez d'autres champs de l'entité Photo ici si nécessaire
            ];
        }
        $peluchesPhotos = $paginator->paginate(
            $peluchesPhotos, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );
        $peluchesCategory = $em->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $em->getRepository(Category::class)->findOneBy(['id' => '1']);
        return $this->render("products/categorie.html.twig", [
            'peluchesPhotos' => $peluchesPhotos,
            'peluchesCategory' => $peluchesCategory,
            'doudousCategory' => $doudousCategory,
            'category' => $category,
        ]);
    }

    /**
     * @Route("search", name="app_search")
     */
    public function searchAction(Request $request, PhotosRepository $photosRepository)
    {
        $searchTerm = $request->query->get('searchTerm');
        
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->where('p.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');
        
        $products = $queryBuilder->getQuery()->getResult();
        $photos = $photosRepository->findBy(['product' => $products]);
        $peluchesCategory = $entityManager->getRepository(Category::class)->findOneBy(['id' => '2']);
        $doudousCategory = $entityManager->getRepository(Category::class)->findOneBy(['id' => '1']);
        // dd($photos);
        return $this->render('products/search.html.twig', [
            'products' => $products,
            'photos' => $photos,
            'searchTerm' => $searchTerm,
            'peluchesCategory' => $peluchesCategory,
            'doudousCategory' => $doudousCategory,
        ]);
    }


}
