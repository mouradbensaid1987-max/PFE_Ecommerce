<?php

namespace App\Service;



use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Refund;

class StripePayment
{

    public $redirecturl;
    // Stripe limite chaque valeur de metadata à 500 caractères.
    // On laisse une marge de sécurité en découpant à 450.
    private const METADATA_CHUNK_SIZE = 450;


    public function __construct(){
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);//Tu donnes à Stripe ta clé secrète.
        Stripe::setApiVersion('2026-06-24.dahlia'); //on fixe la version de Stripe pour éviter les bugs futurs.
    }

    public function startPayment(array $checkoutData, $p): void
    {
      $metadata = [
            'reference' => $checkoutData['reference'],
            'user_id' => (string) $checkoutData['userId'],
            'shipping_cost' => $checkoutData['shippingCost'],
            'total_ttc' => $checkoutData['totalTtc'],
            'delivery_first_name' => $checkoutData['deliveryFirstName'],
            'delivery_last_name' => $checkoutData['deliveryLastName'],
            'delivery_street' => $checkoutData['deliveryStreet'],
            'delivery_postal_code' => $checkoutData['deliveryPostalCode'],
            'delivery_city' => $checkoutData['deliveryCity'],
            'delivery_country' => $checkoutData['deliveryCountry'],
      ];
      
      $cartItems = [];
      foreach ($checkoutData['cart'] as $item) {
          $cartItems[] = $item['productId'] . ':'
                      . $item['quantity'] . ':'
                      . $item['priceTtc'] . ':'
                      . $item['tva'];
      }
      $cartString = implode('|', $cartItems);

      $chunks = str_split($cartString, self::METADATA_CHUNK_SIZE);
      foreach ($chunks as $index => $chunk)
        {
            $metadata['cart_' . $index] = $chunk;
        }
      $metadata['cart_chunks'] = (string) count($chunks); 



      $session = Session::create(
        [
          'customer_email' => $checkoutData['userEmail'],
          'line_items' => array_map(fn(array $item) =>
              [
                'quantity' => $item['quantity'],
                'price_data' => 
                    [
                      'currency' => 'eur',
                      'product_data' => 
                          [
                             'name' => "Produit : " . $item['name'], 
                          ],
                      'unit_amount' => $item['priceTtc'] * 100,
                    ],
              ],$checkoutData['cart']),
          'mode' => 'payment',
          'payment_method_types' => [$p],
          'cancel_url' => 'https://localhost:8000/pay/cancel',
          'success_url' => 'https://localhost:8000/pay/check?ref=' . $checkoutData['reference'],
          'shipping_options' => 
            [
              [
                'shipping_rate_data' => 
                [
                    'type' => 'fixed_amount',
                    'fixed_amount' => [
                        'amount' => $checkoutData['shippingCost'] * 100, 
                        'currency' => 'eur',
                    ],
                    'display_name' => 'Frais de livraison',
                ],
              ],
            ],
          'payment_intent_data'=>
            [                
              'metadata' => $metadata,
            ],
        ]);
        $this->redirecturl = $session->url;
    }

    public function getStripeRedirectUrl(){
        return $this->redirecturl;
    }


}