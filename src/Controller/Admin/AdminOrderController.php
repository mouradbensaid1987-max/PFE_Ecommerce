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
      public function index(OrderRepository $repo, Request $request): Response
      {
          $search = $request->query->get('q'); // nom / référence de la commande
          $status = $request->query->get('status'); // état de la commande
          $userQuery = $request->query->get('user'); // email ou nom du client

          $qb = $repo->createQueryBuilder('o')
                     ->leftJoin('o.user', 'u')->addSelect('u')
                     ->orderBy('o.createdAt', 'DESC');
        
          // Recherche par nom / référence de commande (ex: "CMD-A1B2" ou juste "A1B2")
          if ($search) 
            {
              $qb->andWhere('o.reference LIKE :search')
                 ->setParameter('search', '%' . $search . '%');
            }

          // Filtre par état de la commande (pending, paid, preparing, shipped, ...)
          if ($status) 
            {
              $qb->andWhere('o.status = :status')
                 ->setParameter('status', $status);
            }
          // Recherche par client : email, prénom ou nom
          if ($userQuery) 
            {
              $qb->andWhere('u.email LIKE :userQuery OR u.firstName LIKE :userQuery OR u.lastName LIKE :userQuery')
                 ->setParameter('userQuery', '%' . $userQuery . '%');
            }
        
        return $this->render('admin/order/index.html.twig', [
              'orders' => $qb->getQuery()->getResult(),
              'search' => $search,
              'status' => $status,
              'userQuery' => $userQuery,
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