<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class ProductVoter extends Voter
{
    public const CREATE = 'PRODUCT_CREATE';
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';
    private const SUPPORTED_ATTRIBUTES = [
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];
    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager
    ) {
    }
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute,self::SUPPORTED_ATTRIBUTES,true)) {
            return false;
          }
        if (in_array($attribute, [self::EDIT,self::DELETE], true)) {
            return $subject instanceof Product;
          }
        return $subject === null; 
    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool 
    {
        $utilisateur = $token->getUser(); 

        if (!$utilisateur instanceof User) {
            return false;
        }
        if ($this->isGranted($token, User::ROLE_ADMIN)) {
            return true;
          }
        return match ($attribute) {
            self::CREATE => false,
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
