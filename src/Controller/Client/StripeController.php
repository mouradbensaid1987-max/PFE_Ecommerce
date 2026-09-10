<?php

namespace App\Controller\Client;


use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
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
use Stripe\Checkout\Session;

final class StripeController extends AbstractController
{
    #[Route('/pay/check', name: 'pay_check', methods: ['GET'])]
    public function checkPayment( Request $request, OrderRepository $orderRepository): Response
    {
      
        $referance = $request->query->get('ref');

        if (!$referance) {
            throw $this->createNotFoundException('Session Stripe introuvable.');
        }
        // On récupère la commande liée à cette session (stockée lors de la création de la session)
        $order = $orderRepository->findOneBy(['reference' => $referance]);

        // On se fie à l'état mis à jour par le WEBHOOK, pas à Stripe directement
        if (!$order) {
            // Au lieu d'un échec immédiat, on redirige vers success quand même :
            // le webhook va créer la commande dans les secondes qui suivent,
            // et l'email de confirmation partira normalement.
            return $this->render('stripe/success_pending.html.twig');
        }

        if ($order->getStatus() !== 'paid') {
            return $this->redirectToRoute('app_stripe_cancel');
        }

        return $this->redirectToRoute('app_stripe_success');
    }

    #[Route('/pay/success', name: 'app_stripe_success')]
    public function index(SessionInterface $session): Response
    {
         dump($this->getUser());
        dump($session->getId());
        dump($session->all());

        $session->set('cart', []);
        $session->remove('checkout_order');
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    // stripe listen --forward-to https://127.0.0.1:8000/stripe/notify
      // stripe listen --forward-to https://localhost:8000/stripe/notify

    #[Route('/stripe/notify', name: 'app_stripe_notify', methods: ['POST'])]
    public function stripeNotify(
        Request $request,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): Response 
  {
      Stripe::setApiKey($_SERVER['STRIPE_SECRET']);

      $endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];
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
                  
                  $metadata = $paymentIntent->metadata;


                  // 🔐 sécurisation metadata
                  if (!isset($metadata->reference))
                    {
                        return new Response('Metadata manquante', 200);
                    }


                  $existingOrder = $orderRepository->findOneBy(['reference' => $metadata->reference]);
                  if ($existingOrder)
                  {
                      return new Response('Commande déjà créée', 200);
                  }

                  $totalTtc = (float) $metadata->total_ttc;
                  $stripeTotalAmount = $paymentIntent->amount / 100;

                  if ($totalTtc !== $stripeTotalAmount)
                  {
                      return new Response('Montant incohérent', 200);
                  }

                  $user = $userRepository->find((int) $metadata->user_id);
                  if (!$user)
                  {
                      return new Response('Utilisateur introuvable', 200);
                  }

                  // Reconstruction du panier à partir des morceaux de
                  // metadata (cart_0, cart_1, ... voir StripePayment).
                  $chunkCount = (int) ($metadata->cart_chunks ?? '0');
                  $cartString = '';
                  for ($i = 0; $i < $chunkCount; $i++)
                    {
                        $key = 'cart_' . $i;
                        $cartString .= $metadata->$key ?? '';
                    }

              // ---- Création de la VRAIE commande, uniquement ici ----

              $order = new Order();
              $order->setReference($metadata->reference);
              $order->setUser($user);
              $order->setShippingCost($metadata->shipping_cost);
              $order->setTotalTtc($metadata->total_ttc);
              $order->setStatus(Order::STATUS_PAID);

              $order->setDeliveryFirstName($metadata->delivery_first_name);
              $order->setDeliveryLastName($metadata->delivery_last_name);
              $order->setDeliveryStreet($metadata->delivery_street);
              $order->setDeliveryPostalCode($metadata->delivery_postal_code);
              $order->setDeliveryCity($metadata->delivery_city);
              $order->setDeliveryCountry($metadata->delivery_country);

              foreach (explode('|', $cartString) as $line)
                  {
                      if ($line === '')
                        {
                            continue;
                        }

                      [$productId, $quantity, $priceTtc, $tva] = explode(':', $line);
                      $product = $productRepository->find((int) $productId);

                      if (!$product) {
                          return new Response('Produit introuvable', 200);
                      }

                      $quantity = (int) $quantity;

                      // Vérification du stock
                      if ($product->getStock() < $quantity) {
                          return new Response(
                              'Stock insuffisant pour le produit : ' . $product->getName(),
                              200
                          );
                      }
                      
                      $orderItem = new OrderItem();
                      $orderItem->setProduct($product);
                      $orderItem->setTva($tva);
                      $orderItem->setPriceTtc($priceTtc);
                      $orderItem->setQuantity((int) $quantity);
                      $order->addItem($orderItem);
                      $product->setStock($product->getStock() - (int) $quantity);
                  }

              $entityManager->persist($order);
              $entityManager->flush();
              $this->sendConfirmationEmail($order, $mailer);
            break;

        default:

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