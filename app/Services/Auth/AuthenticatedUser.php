<?php

namespace App\Services\Auth;

class AuthenticatedUser
{
    private static ?int $userId = null;

    public static function setId(int $id): void
    {
        self::$userId = $id;
    }

    public static function getId(): ?int
    {
        return self::$userId;
    }
}
