<?php

namespace App\Repositories\Token;

use stdClass;

interface TokenRepositoryInterface
{
    public function createToken(int $userId, string $token): void;

    public function getToken(string $token): ?StdClass;

    public function getUserByToken(string $token): ?int;
}
