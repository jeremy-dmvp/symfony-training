<?php

declare(strict_types=1);

namespace Infrastructure\Lexik;

use Application\Security\JWTEncoder;
use Application\Security\User;
use Domain\Collection\UsersCollection;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Override;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final readonly class LexikJWTEncoder implements JWTEncoder
{
    public function __construct(
        private JWTTokenManagerInterface $jwtTokenManager,
        private UsersCollection $users,
    ) {
    }

    #[Override]
    public function encode(User $user): string
    {
        return $this->jwtTokenManager->create($user);
    }

    #[Override]
    public function decode(string $jwt): User
    {
        $payload = $this->jwtTokenManager->parse($jwt);
        $identifier = $payload[$this->jwtTokenManager->getUserIdClaim()] ?? null;

        if (null === $identifier) {
            throw new UserNotFoundException();
        }

        $user = $this->users->findOneByEmail($identifier);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return new User($user);
    }
}
