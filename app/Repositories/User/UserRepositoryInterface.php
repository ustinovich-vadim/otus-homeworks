<?php

namespace App\Repositories\User;

use App\DTO\UserRegisterDTO;
use stdClass;

interface UserRepositoryInterface
{
    public function create(UserRegisterDTO $userRegisterDTO, string $hashedPassword): void;
    public function findByEmail(string $email): ?stdClass;
    public function findById(int $id): ?stdClass;
}