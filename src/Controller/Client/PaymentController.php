<?php

namespace App\Controller\Client;



use App\Service\StripePayment;
use App\Form\CheckoutType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{

  #[Route('/payment', name: 'app_payment')]
  public function show(SessionInterface $session, StripePayment $payment, Request $request): Response
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
          //  $b = $form->get('typePaiement');
            $a = $form->get('typePaiement')->getData();
          //  dd($b, $a);
            $payment = new StripePayment();
            $payment->startPayment($checkoutData,$a);
            $stripeRedirectUrl = $payment->getStripeRedirectUrl();
            return $this->redirect($stripeRedirectUrl);
        }

      return $this->render('payment/show.html.twig', [
      'order' => $checkoutData,
      'form' => $form,
      ]);
  }
  
}