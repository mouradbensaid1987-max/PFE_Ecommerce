<?php 

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private const SHIPPING_FEE = 4.90;
    private const FREE_SHIPPING_THRESHOLD = 50.0; // livraison gratuite au-delà de ce montant

    public function __construct(private readonly ProductRepository $productRepository, private RequestStack $requestStack)
        { 
          
        }

    public function getcart(SessionInterface $session) : array
    {
        $cart = $session->get('cart', []);
            $cartWhitData =[];
            foreach( $cart as $id=>$quantity)
                {
                    $cartWhitData[]=[
                        'product'=> $this->productRepository->find($id),
                        'quantity'=>$quantity
                    ];
                }

        $total = array_sum(array_map(function($item){
      
            return $item['product']->getPriceTtc() * $item['quantity'];
            },$cartWhitData
            ));
        
        if ($total >= self::FREE_SHIPPING_THRESHOLD) {
            $shippingFee = 0.0;
        } else {
            $shippingFee = self::SHIPPING_FEE;
        }
        $totalWithShipping = round($total + $shippingFee, 2);


            return [
                'cart'=>$cartWhitData,
                'total'=>$total,
                'shippingFee'=> $shippingFee,
                'totalWithShipping' => $totalWithShipping
            ];
    }

    public function getCount(): int
    {
         $cart = $this->requestStack->getSession()->get('cart', []);
         
        return count($cart);
    }

  

}