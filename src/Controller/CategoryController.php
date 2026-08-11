<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\FavoriteRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
  #[Route('/categorie/{id}', name: 'app_category_show')]
  public function show(Category $category, ProductRepository $productRepository, FavoriteRepository $favoriteRepo): Response
  {
    $products = $productRepository->findBy([
      'category' => $category,
      'isActive' => true,
    ]);

    $favoriteIds = [];
        if ($this->getUser()) {
            $favoriteIds = $favoriteRepo->produit_favorie_user($this->getUser());
        }


    return $this->render('category/show.html.twig', [
      'category' => $category,
      'products' => $products,
      'favoriteIds' => $favoriteIds,
    ]);
  }
  
}
