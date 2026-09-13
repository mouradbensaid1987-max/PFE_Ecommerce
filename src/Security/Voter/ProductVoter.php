<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
//use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class ProductVoter extends Voter
{
  // constantes de permission
    public const LIST = 'PRODUCT_LIST';
    public const VIEW = 'PRODUCT_VIEW';
    public const CREATE = 'PRODUCT_CREATE';
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';
 
    // attributs supportés
    private const SUPPORTED_ATTRIBUTES = [
        self::LIST,
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager
    ) {
    }

    /**
     * Vérifie si l'attribut et le sujet sont supportés
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute,self::SUPPORTED_ATTRIBUTES,true)) 
          {
            return false;
          }

        /*
         * Ces actions concernent une agence précise.
         */
        if (in_array($attribute, [self::VIEW,self::EDIT,self::DELETE], true)) 
          {
            // Vérifie que le sujet est une instance d'Agence
            return $subject instanceof Product;
          }

        /*
         * LIST et CREATE ne nécessitent pas
         * d’agence déjà existante.
         */
        return $subject === null; // Retourne true si le sujet est null (pas d'agence spécifique)
    }

    /**
     * Vérifie les permissions basées sur l'attribut et le sujet
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool 
    {
        $utilisateur = $token->getUser(); 


        if (!$utilisateur instanceof User) {
            return false;
        }

      
        if ($this->isGranted($token, User::ROLE_ADMIN)) 
          {
            return true;
          }

        return match ($attribute) {
          
            self::LIST, self::VIEW => false,
            self::CREATE => true,
            self::EDIT => false,
            self::DELETE => false,
            default => false,
        };
    }

    private function isGranted(
        TokenInterface $token,
        string|array $role
    ): bool {
        return $this->accessDecisionManager->decide(
            $token,
            [$role]
        );
    }
}
