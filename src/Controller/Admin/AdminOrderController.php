<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/orders')]
class AdminOrderController extends AbstractController
{

      #[Route('', name: 'app_admin_order_index')]
      public function index(OrderRepository $orderRepository): Response
      {
          $oo = $orderRepository->findBy([], ['createdAt' => 'DESC']);

          return $this->render('admin/order/index.html.twig', [
              'orders' => $oo,
          ]);
      }

      #[Route('/{id}/status', name: 'app_admin_order_status', methods: ['POST'])]
      public function changeStatus(Order $order, Request $request, EntityManagerInterface $em):Response
      {
        $newStatus = $request->request->get('status');
        $valid = [
                    Order::STATUS_PENDING, 
                    Order::STATUS_PAID, 
                    Order::STATUS_SHIPPED,
                    Order::STATUS_DELIVERED, 
                    Order::STATUS_CANCELLED
                ];
        if (in_array($newStatus, $valid, true)) 
          {
              $order->setStatus($newStatus);
              $em->flush();
              $this->addFlash('success', 'Statut mis à jour');
          }

        return $this->redirectToRoute('app_admin_order_index');



      }








}