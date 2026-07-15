<?php 

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
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

            return [
                'cart'=>$cartWhitData,
                'total'=>$total 
            ];
    }

    public function getCount(): int
    {
         $cart = $this->requestStack->getSession()->get('cart', []);

        return array_sum($cart);
    }

}