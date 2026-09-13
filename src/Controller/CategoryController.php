<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\FavoriteRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
  #[Route('/categorie/{slug}-{id}', name: 'app_category_show',  requirements: ['id' => '\d+', 'slug' => '[A-Za-z0-9-]+'])]
  public function show(Category $category,CategoryRepository $categoryRepository, ProductRepository $productRepository, FavoriteRepository $favoriteRepo,string $slug, int $id): Response
  {
    $category = $categoryRepository->find($id);
    if(!$category) 
        {
            throw $this->createNotFoundException("Le category avec l'id {$id} n'existe pas.");
        }
    if($category->getSlug() !== $slug) 
        {
            return $this->redirectToRoute('app_category_show', ['slug' => $category->getSlug(), 'id' => $category->getId()]);
        }

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
