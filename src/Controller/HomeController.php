<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\FavoriteRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository,ProductRepository $productRepository, 
                          FavoriteRepository $favoriteRepo ): Response
    {
      $favoriteIds = [];
      if ($this->getUser()) {
          $favoriteIds = $favoriteRepo->produit_favorie_user($this->getUser());
      }

        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'products' => $productRepository->findBy(
              ['isActive' => true],
              ['createdAt'=> 'DESC'],
              60
            ),
            'favoriteIds' => $favoriteIds,
        ]);
    }
}
