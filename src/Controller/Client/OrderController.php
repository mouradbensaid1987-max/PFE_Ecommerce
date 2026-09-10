<?php

namespace App\Controller\Client;


use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Service\Cart;

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
      
      $data = $cart->getcart($session);
      
    
      if (empty($data) || $data['total']==0) 
        {
          $this->addFlash('warning', 'Votre panier est vide.');
          return $this->redirectToRoute('app_cart_index');

        } elseif($data['cartUpdated'])
        {
            $this->addFlash('warning','Votre panier a été mis à jour : certains produits ne sont plus disponibles.');
        }

      
      $deliveryAddresses = $addressRepository->findBy([
          'user' => $this->getUser()
        ]);


      return $this->render('order/checkout.html.twig', [
            'items'=>$data['cart'],
            'total'=>$data['total'],
            'deliveryAddresses' => $deliveryAddresses,
            'shippingFee'=> $data['shippingFee'],
            'totalWithShipping' => $data['totalWithShipping'],
        ]);
    }


    #[Route('/confirm', name: 'app_order_confirm', methods: ['POST'])]
    public function confirm( Request $request, AddressRepository $addressRepository,
                             Cart $cart,SessionInterface $session
                        ): Response {

        $addressId = (int) $request->request->get('address_id');
        $address = $addressRepository->find($addressId);

        if (!$address || $address->getUser() !== $this->getUser()) 
          {
              throw $this->createAccessDeniedException();
          }
        
        $data =$cart->getcart($session);

        if (empty($data) || $data['total']==0) 
        {
          $this->addFlash('warning', 'Votre panier est vide.');
          return $this->redirectToRoute('app_cart_index');

        } elseif($data['cartUpdated'])
        {
            $this->addFlash('warning','Votre panier a été mis à jour : certains produits ne sont plus disponibles.');
        }

        $cartData = [];
        foreach ($data['cart'] as $item)
          {
                $product = $item['product'];
                $cartData[] = [
                      'productId' => $product->getId(),
                      'name' => $product->getName(),
                      'quantity' => $item['quantity'],
                      'priceTtc' => (string) $product->getPriceTtc(),
                      'tva' => (string) $product->getTva()->getRate(),
                ];
          }
          $checkoutData = [
                'reference' => 'CMD-' . strtoupper(bin2hex(random_bytes(4))),
                'userId' => $this->getUser()->getId(),
                'userEmail' => $this->getUser()->getEmail(),
                'totalTtc' => (string) $data['totalWithShipping'],
                'shippingCost' => (string) $data['shippingFee'],
                'deliveryFirstName' => $address->getFirstName(),
                'deliveryLastName' => $address->getLastName(),
                'deliveryStreet' => $address->getStreet(),
                'deliveryPostalCode' => $address->getPostalCode(),
                'deliveryCity' => $address->getCity(),
                'deliveryCountry' => $address->getCountry(),
                'cart' => $cartData,
            ];

          $session->set('checkout_order', $checkoutData);    
          return $this->redirectToRoute('app_payment');
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