<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class DinosaurVoter extends Voter
{
    public const CREATE = 'DINOSAUR_CREATE';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CREATE;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Alternative: vérif des roles directment sur le user
        // $user = $token->getUser();
        // return $user instanceof User && in_array('ROLE_USER', $user->getRoles(), true);

        return match ($attribute) {
            self::CREATE => $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']),
            default => false,
        };
    }
}
