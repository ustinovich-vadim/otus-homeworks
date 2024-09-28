<?php

namespace App\Services\Token;

use App\Repositories\Token\TokenRepositoryInterface;
use Firebase\JWT\JWT;

readonly class TokenService
{

    public function __construct(private TokenRepositoryInterface $tokenRepository)
    {
        //
    }

    public function createToken(int $userId): string
    {
        $payload = [
            'iss' => "monolith",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256', 'unique_key_id');
        $this->tokenRepository->createToken($userId, $token);

        return $token;
    }

    public function isTokenValid(string $token): bool
    {
        $tokenFromDB = $this->tokenRepository->getToken(hash('sha256',$token));

        return !($tokenFromDB === null || ($tokenFromDB->expires_at && $tokenFromDB->expires_at < now()));
    }

    public function getUserIdByToken(string $token): ?int
    {
        return $this->tokenRepository->getUserByToken(hash('sha256', $token));
    }
}
