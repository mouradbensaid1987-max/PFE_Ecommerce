<?php

namespace App\Controller\Client;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Service\Cart;

use App\Service\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;




#[IsGranted('ROLE_USER')]
#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(Cart $cart,SessionInterface $session, AddressRepository $addressRepository): Response
    {
      $data =$cart->getcart($session);
      if (empty($data)) 
        {
          $this->addFlash('warning', 'Votre panier est vide.');
          return $this->redirectToRoute('app_cart_index');
        }
      
      $deliveryAddresses = $addressRepository->findBy([
          'user' => $this->getUser(),
          'type' => 'delivery',
        ]);


      return $this->render('order/checkout.html.twig', [
            'items'=>$data['cart'],
            'total'=>$data['total'],
            'deliveryAddresses' => $deliveryAddresses
        ]);
    }


    #[Route('/confirm', name: 'app_order_confirm', methods: ['POST'])]
    public function confirm( Request $request, AddressRepository $addressRepository,
                             Cart $cart,StripePayment $payment, EntityManagerInterface $em, SessionInterface $session
                        ): Response {

        $addressId = (int) $request->request->get('address_id');
        $address = $addressRepository->find($addressId);

        if (!$address || $address->getUser() !== $this->getUser()) 
          {
              throw $this->createAccessDeniedException();
          }
        
          $data =$cart->getcart($session);

          $order = new Order();
          $order->setUser($this->getUser());
          $order->setTotalTtc((string) $data['total']);
          $order->setStatus(Order::STATUS_PENDING);

          $order->setDeliveryFirstName($address->getFirstName());
          $order->setDeliveryLastName($address->getLastName());
          $order->setDeliveryStreet($address->getStreet());
          $order->setDeliveryPostalCode($address->getPostalCode());
          $order->setDeliveryCity($address->getCity());
          $order->setDeliveryCountry($address->getCountry());

          foreach ($data['cart'] as $item) 
            {
                $orderItem = new OrderItem();
                $orderItem->setProduct($item['product']);
                $orderItem->setTva($item['product']->getTva()->getRate());
                $orderItem->setPriceTtc((string) $item['product']->getPriceTtc());
                $orderItem->setQuantity($item['quantity']);
                $order->addItem($orderItem);
            }

          $em->persist($order);
          $em->flush();
          
        return $this->redirectToRoute('app_payment', ['ref' => $order->getReference()]);

    }

    #[Route('/history', name: 'app_order_history')]
    public function history(OrderRepository $repo): Response
    {
        $orders = $repo->findBy(
              ['user' => $this->getUser()],
              ['createdAt' => 'DESC']
          );

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
          ]);
    }

    #[Route('/{id}', name: 'app_order_show')]
    public function show(string $id, OrderRepository $repo): Response
    {
        $order = $repo->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if (!$order) {
        throw $this->createNotFoundException();
        }
        return $this->render('order/show.html.twig', ['order' => $order]);
    }


}