<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    // Appelé par Symfony AVANT de vérifier le mot de passe.
    public function checkPreAuth(UserInterface $user): void
    {

        if (!$user instanceof User) 
        {
            return;
        }

        if (!$user->isVerified()) 
        {
            // Ce message s'affiche sur le formulaire de connexion,
            // exactement comme une erreur "identifiants incorrects".
            throw new CustomUserMessageAccountStatusException(
            'Votre compte n\'est pas encore vérifié. Merci de consulter votre boîte mail, 
            ou de demander un nouvel email de confirmation.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}