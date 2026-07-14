<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route('', name: 'app_cart_index')]
    public function index(SessionInterface $session,Cart $cart): Response
    {
       
       $data = $cart->getcart($session);

        return $this->render('cart/index.html.twig', [
            'items'=>$data['cart'],
            'total'=>$data['total'] 
        ]);
    }

    #[Route('/add/{id}/', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(int $id,SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id]))
            {
                $cart[$id]++;
            }
            else{
                $cart[$id]=1;
            }
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart_index');
    }
    
    #[Route('/remove/{id}/', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart(int $id,SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id]))
            {
                unset($cart[$id]);
            }
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {

        $session->set('cart', []);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(Product $product, Request $request, SessionInterface $session): Response
    {
        $qty = (int) $request->request->get('quantity', 1);

        $cart = $session->get('cart');

        if ($qty <= 0) {
            unset($cart[$product->getId()]);
        } else {
          $cart[$product->getId()] = $qty;
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }

}
