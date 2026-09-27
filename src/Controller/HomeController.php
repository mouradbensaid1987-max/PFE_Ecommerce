<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\FavoriteRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository,PaginatorInterface $paginator, ProductRepository $productRepository, 
                          FavoriteRepository $favoriteRepo, Request $request ): Response
    {
      $favoriteIds = [];
      if ($this->getUser()) {
          $favoriteIds = $favoriteRepo->produit_favorie_user($this->getUser());
      }
      $products = $productRepository->findBy(['isActive' => true],['createdAt'=> 'DESC']);
      $categories = $categoryRepository->findAll();

      $products = $paginator->paginate(
            $products, 
            $request->query->getInt('page', 1), 
            12 
       );
      

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
            'favoriteIds' => $favoriteIds,
        ]);
    }
}
