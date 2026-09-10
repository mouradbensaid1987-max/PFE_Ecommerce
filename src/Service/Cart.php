<?php 

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart
{
    private const SHIPPING_FEE = 4.90;
    private const FREE_SHIPPING_THRESHOLD = 50.0; // livraison gratuite au-delà de ce montant

    public function __construct(
                                private readonly ProductRepository $productRepository, 
                                private RequestStack $requestStack)
    { 
      
    }

    public function getcart(SessionInterface $session) : array
    {
        $cart = $session->get('cart', []);
        $cartWhitData =[];
        $cartUpdated = false;
        foreach($cart as $id=>$quantity)
            {
              $product = $this->productRepository->find($id);

               if (!$product || !$product->isActive()) {
                  unset($cart[$id]);
                  $cartUpdated = true;
                  continue;
              }
                $cartWhitData[]=[
                    'product'=> $product,
                    'quantity'=>$quantity
                ];
            }
        $session->set('cart', $cart); // mise a jours de la session

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
                'totalWithShipping' => $totalWithShipping,
                'cartUpdated' => $cartUpdated
            ];
    }
    // methode de nettoyage pour elliminer tous les produit desactiver et supprimer par l'admin et 
    //qui se trouve dans la session
    private function cleanInactiveProducts(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);

            if (!$product || !$product->isActive()) {
                unset($cart[$productId]);
            }
        }

        $session->set('cart', $cart);

        return $cart;
    }

    public function getCount(): int

    { 
         $cart = $this->cleanInactiveProducts();
         //$cart = $this->requestStack->getSession()->get('cart', []);
         
        return count($cart);
    }

  

}