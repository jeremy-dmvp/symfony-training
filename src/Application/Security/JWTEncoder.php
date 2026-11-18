<?php

declare(strict_types=1);

namespace Application\Security;
interface JWTEncoder
{
    public function encode(User $user): string;
    public function decode(string $jwt): User;

}
