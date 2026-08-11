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
            'total'=>$data['total'],
            'shippingFee'=> $data['shippingFee'],
            'totalWithShipping' => $data['totalWithShipping'],
        ]);
    }

    #[Route('/add/{id}/', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Product $product, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $id = $product->getId();
        $stock = $product->getStock();

        $currentQty = $cart[$id] ?? 0;
        $newQty = $currentQty + 1;

        if ($stock <= 0) {
            $this->addFlash('danger', 'Le produit "' . $product->getName() . '" est en rupture de stock.');
            return $this->redirectToRoute('app_cart_index');
        }
        if ($newQty > $stock) {
            // On plafonne à la quantité max disponible
            $cart[$id] = $stock;
            $this->addFlash('warning','Stock insuffisant pour "' . $product->getName() . '". Quantité limitée à ' . $stock . '.');
        } else {
            $cart[$id] = $newQty;
            $this->addFlash('success', 'Le produit "' . $product->getName() . '" a été ajouté au panier.');
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

        if ($qty <= 0) 
          {
            unset($cart[$product->getId()]);
            $this->addFlash('info', 'Le produit "' . $product->getName() . '" a été retiré du panier.');
          } else {
            $stock = $product->getStock();

            if ($qty > $stock) 
            {
                $cart[$product->getId()] = $stock;
                $this->addFlash('warning','Stock insuffisant pour "' . $product->getName() . '". Quantité ajustée à ' . $stock . '.');
            } else {
              $cart[$product->getId()] = $qty;
            }
        }
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }

}
