<?php

namespace App\Service;


use App\Entity\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Refund;

class StripePayment
{

    public $redirecturl;

    public function __construct(){
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);//Tu donnes à Stripe ta clé secrète.
        Stripe::setApiVersion('2026-06-24.dahlia'); //on fixe la version de Stripe pour éviter les bugs futurs.
    }

    public function startPayment(Order $order, $p)
    {
      $table = [];
      foreach($order->getItems() as $ligne)
        {
                $ProductItem = [];
                $ProductItem['name'] = $ligne->getProduct()->getName();
                $ProductItem['slug'] = $ligne->getProduct()->getSlug();
                $ProductItem['prix'] = $ligne->getPriceTtc();
                $ProductItem['qte'] = $ligne->getQuantity();
                $table[]= $ProductItem;
        }
      
      $session = Session::create(
        [
          'customer_email' => $order->getUser()->getEmail(),
          'line_items' => array_map(fn(array $table) =>
              [
                'quantity' => $table['qte'],
                'price_data' => 
                    [
                      'currency' => 'eur', //devise
                      'product_data' => 
                          [
                              'name' => "Produit : " . $table['name'], // nom produit affiché sur Stripe
                          ],
                      'unit_amount' => $table['prix'] * 100,
                      
                    ],
                
              ],$table),
              'mode' => 'payment',
              'payment_method_types' => [$p],
              'cancel_url' => 'https://127.0.0.1:8000/pay/cancel',
              'success_url' => 'https://127.0.0.1:8000/pay/success',
              'payment_intent_data'=>
                [
                    'metadata' => //très important pour plus tard on peux stocké ['user_id' => 5, 'order_id' => 123]
                            [
                                'orderId' => $order->getId(),
                                'user_id' => $order->getUser()->getId(),
                            ],
                ],
        ]);
        $this->redirecturl = $session->url;
      
    }

  
  

    public function getStripeRedirectUrl(){
        return $this->redirecturl;
    }


}