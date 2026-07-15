<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminDashboardController extends AbstractController
{

    #[Route('', name: 'app_admin_dashboard')]
    public function index(ProductRepository $products, 
                          CategoryRepository $categories,
                          UserRepository $users,
                          OrderRepository $orders): Response 
        {
            return $this->render('admin/dashboard.html.twig', [
            'nbProducts' => $products->count([]),
            'nbCategories' => $categories->count([]),
            'nbUsers' => $users->count([]),
            'nbOrders' => $orders->count([]),
            ]);
            
        }
}