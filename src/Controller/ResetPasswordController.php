<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
 //use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    //use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail($email,$mailer);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
      // 􀀁 si aucun token n'est en session, on affiche quand même la
      // page "vérifiez vos emails" avec un token factice : cela
      // empêche de deviner si une adresse email existe en base ou non

      $resetToken = $this->getTokenObjectFromSession();

        if (null === $resetToken) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher,?string $token = null): Response
    {

        if ($token) 
          {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
          }

        $token = $this->getTokenFromSession();
      
        if (null === $token) 
          {
            throw $this->createNotFoundException('Aucun token de réinitialisation trouvé.');
          }

        try {
        
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
            /*
            validateTokenAndFetchUser($token)
                    │
                    ▼
            Décompose $token → selector + verifier
                    │
                    ▼
            SELECT en base via le selector
                    │
              ┌────┴────┐
              │ trouvé ? │── NON ──► Exception (token inconnu)
              └────┬────┘
                    │ OUI
                    ▼
            expires_at dépassé ?
              │
              ├── OUI ──► Exception (token expiré)
              │
              └── NON
                    │
                    ▼
            Hash(verifier reçu) == hashed_token stocké ?
              │
              ├── NON ──► Exception (token invalide)
              │
              └── OUI
                    │
                    ▼
            Récupère le $user lié (via user_id)
                    │
                    ▼
            Retourne l'objet $user

             */
      

        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('danger', 'Ce lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

    
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // 􀀁 le token est à usage unique : on le supprime immédiatement
            $this->resetPasswordHelper->removeResetRequest($token);
            // supprimer la demande de reset mot de passe de la BDD
      
            $plainPassword = $form->get('plainPassword')->getData();
            
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();
          
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé. Vous pouvez vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // 􀀁 on redirige TOUJOURS vers la même page, que l'email existe
        // ou non en base : cela empêche un attaquant de découvrir quelles
        // adresses email sont inscrites sur le site (énumération de comptes)
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            /*
            * generateResetToken est une méthode qui :
                  1- Génère une chaîne de caractères aléatoire et sécurisée ("token").
                  2- Enregistre en base de données (table reset_password_request) une entrée liée à cet utilisateur avec ce token 
                    , accompagnée d'une date d'expiration
                  3- Retourne un objet représentant cette demande de réinitialisation.
            * $user est le paramètre passé :
                  c'est l'utilisateur pour lequel on veut générer le token.
            
            * $resetToken : un objet de type ResetPasswordToken, qui contient plusieurs informations
                  getToken() → le token à mettre dans l'URL envoyée par email
                  getExpiresAt() → la date d'expiration

            ////////////////////////////////////////////////////////////////
            generateResetToken($user)
                    │
                    ▼
            Génère : selector + verifier (aléatoire)
                    │
                    ▼
            Hache le verifier ──────────► hashed_token
                    │
                    ▼
            INSERT dans reset_password_request
              (user_id, selector, hashed_token, requested_at, expires_at)
                    │
                    ▼
            Retourne un objet ResetPasswordToken
              contenant le token COMPLET (non haché) → à mettre dans l'email
            ///////////////////////////////////////////////////////////////////
          Et quand l'utilisateur clique sur le lien reçu par email ?

          1- Symfony décompose le token recu en deux partie selector + verifier 
          2- Il fait une requête SQL pour recuperé hashed_token a partire de selector
          3- Il re-hache le verifier reçu et le compare au hashed_token stocké en base
          4- Si ça correspond et que expires_at n'est pas dépassé → le token est valide, l'utilisateur peut changer son mot de passe.
          5- Une fois le mot de passe changé, la ligne dans reset_password_request est supprimée (nettoyage), via removeResetPasswordRequest().
          


            */

        } catch (ResetPasswordExceptionInterface $e) {
          
            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@fix-pro.fr', 'FixPro'))
            ->to((string) $user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);

        $this->setTokenObjectInSession($resetToken);
        //Stocke l'objet $resetToken dans la session de l'utilisateur.

        return $this->redirectToRoute('app_check_email');
    }





    public function getTokenObjectFromSession(): ?ResetPasswordToken
    {
        return $this->getSessionService()->get('ResetPasswordToken');
    }
    public function getSessionService(): SessionInterface
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return $request->getSession();
    }
    public function storeTokenInSession(string $token): void
    {
        $this->getSessionService()->set('ResetPasswordPublicToken', $token);
    }
    public function getTokenFromSession(): ?string
    {
        return $this->getSessionService()->get('ResetPasswordPublicToken');
    }
    public function cleanSessionAfterReset(): void
    {
        $session = $this->getSessionService();

        $session->remove('ResetPasswordPublicToken');
        $session->remove('ResetPasswordCheckEmail');
        $session->remove('ResetPasswordToken');
    }
    public function setTokenObjectInSession(ResetPasswordToken $token): void
    {
        $token->clearToken();

        $this->getSessionService()->set('ResetPasswordToken', $token);
    }

  
}

