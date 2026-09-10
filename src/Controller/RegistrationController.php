<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier,) 
      {
      }


    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            // dd($plainPassword,$user->getPassword());
            $user->setRoles(['ROLE_USER']);
            $user->setVerified(false);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                ->from(new Address('no-reply@fix-pro.fr', 'FixPro'))
                ->to((string) $user->getEmail())
                ->subject('Confirmez votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Compte créé ! Vérifiez votre boîte mail pour activer votre compte avant de vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
      $id = $request->query->get('id');
    
      if (null === $id) 
      {
          return $this->redirectToRoute('app_register');
      }
      
      $user = $entityManager->getRepository(User::class)->find($id);
    
      if (null === $user) 
      {
          return $this->redirectToRoute('app_register');
      }

      try {

          $this->emailVerifier->handleEmailConfirmation($request, $user);

      } catch (\SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface $exception) {
          $this->addFlash('danger', 'Le lien de vérification est invalide ou a expiré.');
      
          return $this->redirectToRoute('app_verify_email_resend');
      }

      $this->addFlash('success', 'Votre adresse email a bien été vérifiée. Vous pouvez maintenant vous connecter.');

      return $this->redirectToRoute('app_login');

    }


    #[Route('/verify/email/resend', name: 'app_verify_email_resend')]
    public function resendVerifyEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
      if ($request->isMethod('POST')) 
      {
          $email = (string) $request->request->get('email');
          $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

          if ($user && !$user->isVerified()) 
            {
              $this->emailVerifier->sendEmailConfirmation(
              'app_verify_email',
              $user,
              (new TemplatedEmail())
                    ->from(new Address('no-reply@fix-pro.fr', 'FixPro'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirmez votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                );
          }
          $this->addFlash('success', 'Si un compte existe avec cette adresse et n\'est pas encore vérifié, 
                                      un nouvel email de confirmation vient de vous être envoyé.');
          
          return $this->redirectToRoute('app_login');
      }

      return $this->render('registration/resend_verify_email.html.twig');
    }


}
