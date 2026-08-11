<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\FavoriteRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product_list')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository,
                          PaginatorInterface $paginator, Request $request, FavoriteRepository $favoriteRepo): Response
    {
        $filters = [
              'search' => $request->query->get('q'),
              'category' => $request->query->get('category'),
              'materiau' => $request->query->get('materiau'),
              'diametreMm' => $request->query->get('diametreMm'),
              'typeTete' => $request->query->get('typeTete'),
              'typeEmpreinte' => $request->query->get('typeEmpreinte'),
              'unite' => $request->query->get('unite'),
              'longueurMin' => $request->query->get('longueurMin'),
              'longueurMax' => $request->query->get('longueurMax'),
          ];

    $data = $productRepository->findFiltered($filters, true);


    //  $search = $request->query->get('q');
    //  $data = $productRepository->findActiveProducts($search);
    
       $products = $paginator->paginate(
                    $data,
                    $request->query->getInt('page', 1),
                    20
          );
          
        $favoriteIds = [];
        if ($this->getUser()) {
            $favoriteIds = $favoriteRepo->produit_favorie_user($this->getUser());
        }

        return $this->render('product/list.html.twig', [

            'categories' => $categoryRepository->findAll(),
            'products' => $products,
            'search' => $filters['search'],
            'filters' => $filters,
            'materiaux' => $productRepository->findDistinctValues('materiau'),
            'diametres' => $productRepository->findDistinctValues('diametreMm'),
            'typesTete' => $productRepository->findDistinctValues('typeTete'),
            'typesEmpreinte' => $productRepository->findDistinctValues('typeEmpreinte'),
            'unites' => $productRepository->findDistinctValues('unite'),
            'favoriteIds' => $favoriteIds,
        ]);
    }



    #[Route('/produit/{id}', name: 'app_product_show')]
    public function show(Product $product, CategoryRepository $categoryRepository, FavoriteRepository $favoriteRepo): Response
    {

        $favoriteIds = [];
        if ($this->getUser()) {
            $favoriteIds = $favoriteRepo->produit_favorie_user($this->getUser());
        }


        return $this->render('product/show.html.twig', [
        'product' => $product,
        'favoriteIds' => $favoriteIds,
        ]);
    }
}
