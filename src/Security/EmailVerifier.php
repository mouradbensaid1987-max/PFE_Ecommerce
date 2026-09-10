<?php

namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{

    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager) 
    {
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
      // génère une URL signée, valable 1 heure par défaut, contenant
      // l'id et l'email de l'utilisateur de façon infalsifiable
      $signatureComponents = $this->verifyEmailHelper->generateSignature(
          $verifyEmailRouteName,
          (string) $user->getId(),
          (string) $user->getEmail(),
          ['id' => $user->getId()]
      );
      /*
      Ce qui se passe à l'intérieur de generateSignature(), étape par étape : 
          Étape A — Calcul de la date d'expiration
          $expiresAt = time() + $this->lifetime;

          Étape B — Génération d'une URL "brute" (sans signature)
          https://monsite.com/verify/email?id=42&expires=1735900800

          Étape C — Calcul du HMAC (la vraie signature cryptographique)
          une empreinte cryptographique impossible à falsifier sans connaître la clé secrète.
          $signatureValue = hash_hmac(
              'sha256',
              $routeName . $userId . $userEmail . $expiresAt . http_build_query($extraParams),
              $secretKey // dérivé de ton APP_SECRET dans .env
          );

          Étape D — Construction de l'URL finale complète
          https://monsite.com/verify/email?id=42&expires=1735900800&signature=a3f8e9d2c1b4f7e6...

          Étape E — Retour d'un objet VerifyEmailSignatureComponents
          un objet contenant plusieurs informations utiles :
            $signatureComponents->getSignedUrl();   // l'URL complète prête à envoyer par email
            $signatureComponents->getExpiresAt();   // objet DateTimeImmutable de la date d'expiration
            $signatureComponents->getLifetime(); 




      */

      $context = $email->getContext();
      $context['signedUrl'] = $signatureComponents->getSignedUrl();
      $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
      $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();
      $email->context($context);
      $this->mailer->send($email);

    }
    /**
    * @throws VerifyEmailExceptionInterface
    */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        // revérifie la signature de l'URL cliquée : infalsifiable et à durée limitée
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
            $request,
            (string) $user->getId(),
            (string) $user->getEmail()
        );


        $user->setVerified(true);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}