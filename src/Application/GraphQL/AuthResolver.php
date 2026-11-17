<?php

declare(strict_types=1);

namespace Application\GraphQL;

use Application\Security\JWTEncoder;
use Application\Security\User;
use Domain\Collection\UsersCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final readonly class AuthResolver
{
    public function __construct(
        private UsersCollection $users,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTEncoder $jwtEncoder,
    ) {
    }

    public function __invoke(string $email, string $password): string
    {
        $user = $this->users->findOneByEmail($email);

        if (null === $user) {
            throw new BadCredentialsException();
        }

        $securityUser = new User($user);

        if (!$this->passwordHasher->isPasswordValid($securityUser, $password)) {
            throw new BadCredentialsException();
        }

        return $this->jwtEncoder->encode($securityUser);
    }
}
