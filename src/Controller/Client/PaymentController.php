<?php

namespace App\Controller\Client;



use App\Form\CheckoutType;
use App\Repository\ProductRepository;
use App\Service\StripePayment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{

  #[Route('/payment', name: 'app_payment')]
  public function show(SessionInterface $session, ProductRepository $productRepository, StripePayment $payment, Request $request): Response
  {
      $checkoutData = $session->get('checkout_order');
      //dd($checkoutData );
      if (!$checkoutData)
      {
          $this->addFlash('warning', 'Aucune commande en cours, merci de recommencer.');
          return $this->redirectToRoute('app_cart_index');
      }
      
        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
          foreach ($checkoutData['cart'] as $item) {
                $product = $productRepository->find($item['productId']);

                if (!$product || !$product->isActive() || $product->getStock() < $item['quantity']) {
                    $this->addFlash('danger', sprintf(
                        'Le stock de « %s » a changé. Merci de vérifier votre panier avant de payer.',
                        $item['name']
                    ));
                    $session->remove('checkout_order');
                    return $this->redirectToRoute('app_cart_index');
                }
            }
        
            $typedepayement = $form->get('typePaiement')->getData();
          
            $payment = new StripePayment();
            $payment->startPayment($checkoutData,$typedepayement);
            $stripeRedirectUrl = $payment->getStripeRedirectUrl();
            return $this->redirect($stripeRedirectUrl);
        }

      return $this->render('payment/show.html.twig', [
      'order' => $checkoutData,
      'form' => $form,
      ]);
  }
  
}