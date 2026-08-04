<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product_list')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository,PaginatorInterface $paginator, Request $request): Response
    {
      $search = $request->query->get('q');
      $data = $productRepository->findActiveProducts($search);
    
      $products = $paginator->paginate(
          $data,
          $request->query->getInt('page', 1),
          16
      );

        return $this->render('product/list.html.twig', [
        //  'categories' => $categoryRepository->findAll(),
          'products' => $products,
          'search' => $search,
        ]);
    }



    #[Route('/produit/{id}', name: 'app_product_show')]
    public function show(Product $product, CategoryRepository $categoryRepository): Response
    {
        return $this->render('product/show.html.twig', [
        'product' => $product,
        ]);
    }
}
