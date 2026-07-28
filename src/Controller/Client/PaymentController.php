<?php

namespace App\Controller\Client;


use App\Entity\Order;
use App\Service\StripePayment;
use App\Form\CheckoutType;
use Stripe\Stripe;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{

  #[Route('/payment/{ref}', name: 'app_payment')]
  public function show(string $ref, OrderRepository $repo, StripePayment $payment, Request $request): Response
  {
      $order = $repo->findOneBy(['reference' => $ref, 'user' => $this->getUser()]);

      if (!$order) 
        {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);
        //$form1->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) 
            {

                $a = $form->get('typePaiement')->getData();
                $payment = new StripePayment();
                $payment->startPayment($order,$a);
                $stripeRedirectUrl = $payment->getStripeRedirectUrl();
                return $this->redirect($stripeRedirectUrl);
            }

      return $this->render('payment/show.html.twig', [
      'order' => $order,
      'form' => $form,
      ]);
  }
  
/*

  #[Route('/payment/{ref}/pay', name: 'app_payment_pay', methods: ['POST'])]
  public function pay(string $ref, OrderRepository $repo, EntityManagerInterface $em,SessionInterface $session): Response {

    $order = $repo->findOneBy(['reference' => $ref, 'user' => $this->getUser()]);

    if (!$order) 
      {
          throw $this->createNotFoundException();
      }
    $order->setStatus(Order::STATUS_PAID);
    $em->flush();

    $session->set('cart', []);
    $this->addFlash('success', 'Paiement validé ! Commande ' . $order->getReference() . 'confirmée.');

    return $this->redirectToRoute('app_order_history');

  }
    */


}