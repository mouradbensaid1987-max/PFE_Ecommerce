<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
          return $this->redirectToRoute('app_home');
        }

        $customError = null;
        if ($error) {
            // Si c'est notre exception personnalisée (compte non vérifié),
            // on affiche le message exact qu'on a défini dans UserChecker.
            if ($error instanceof CustomUserMessageAccountStatusException) {
                $customError = $error->getMessage();
            } else {
                // Pour toutes les autres erreurs (mauvais mot de passe, etc.),
                // on garde un message générique.
                $customError = 'Votre email ou mot de passe est incorrect.';
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $customError,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
