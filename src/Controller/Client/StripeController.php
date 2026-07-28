<?php

namespace App\Controller\Client;


use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;


final class StripeController extends AbstractController
{
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function index(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->render('stripe/index.html.twig');
    }

    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    // stripe listen --forward-to https://127.0.0.1:8000/stripe/notify

    #[Route('/stripe/notify', name: 'app_stripe_notify', methods: ['POST'])]
    public function stripeNotify(
        Request $request,
        OrderRepository $orderrepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): Response 
    {
      Stripe::setApiKey($_SERVER['STRIPE_SECRET']);

      $endpoint_secret = 'whsec_ebaad970debc090072f3bb31ade1a7329632d6f2b5b23e775d372491f00e8d4f';
      $payload = $request->getContent();
      $sig_header = $request->headers->get('stripe-signature');

      try {
          $event = \Stripe\Webhook::constructEvent(
              $payload,
              $sig_header,
              $endpoint_secret
          );
      } catch (\UnexpectedValueException $e) {
          return new Response('Payload invalide', 400);
      } catch (\Stripe\Exception\SignatureVerificationException $e) {
          return new Response('Signature invalide', 400);
      }
      
      switch ($event->type) 
      {
        case 'payment_intent.succeeded':

              $paymentIntent = $event->data->object;
              // 🔐 sécurisation metadata
              if (!isset($paymentIntent->metadata->orderId)) 
                {
                    return new Response('Metadata manquante', 200);
                }
              $orderId = $paymentIntent->metadata->orderId;
              $order = $orderrepository->find($orderId);

              // 🔐 sécurisation commande
              if (!$order) 
                {
                  return new Response('Commande introuvable', 200);
                }

              if ($order->getStatus() === 'paid') 
                {
                    return new Response('Commande déjà traitée', 200);
                }

              $totalttc = $order->getTotalTtc();
              $stripeTotalAmount = $paymentIntent->amount / 100;

              if ($totalttc == $stripeTotalAmount) 
                {
                    $order->setStatus('paid');
                    $order->setCreatedAt(new \DateTimeImmutable());
                    foreach ($order->getItems() as $item) 
                    {
                        $item->getProduct()->setStock($item->getProduct()->getStock() - $item->getQuantity());
                    }

                    $entityManager->flush();

                    $this->sendConfirmationEmail($order, $mailer);
                }
              
              break;

        default:
            // ⚠️ toujours retourner 200 sinon Stripe retry
            return new Response('Event ignoré', 200);
      }
      return new Response('OK', 200);
    }

    private function sendConfirmationEmail(Order $order, MailerInterface $mailer): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tonsite.fr', 'Ton Site'))
            ->to(new Address($order->getUser()->getEmail()))
            ->subject('Confirmation de votre commande # ' . $order->getReference())
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                  'order' => $order,
              ])
            ;

        try {
            $mailer->send($email);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            // logge l'erreur mais ne bloque pas le webhook Stripe
            // ex: $this->logger->error('Erreur envoi email: '.$e->getMessage());
        }
    }

}