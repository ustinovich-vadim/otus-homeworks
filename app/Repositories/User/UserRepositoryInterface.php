<?php

namespace App\Repositories\User;

use App\DTO\UserRegisterDTO;
use stdClass;

interface UserRepositoryInterface
{
    public function create(UserRegisterDTO $userRegisterDTO, string $hashedPassword): void;
    public function findByEmail(string $email): ?stdClass;
    public function findById(int $id): ?stdClass;
    public function searchByFirstAndLastName(string $name, string $surname): array;
    public function getUsersChunked(int $chunkSize, callable $callback): void;
}
