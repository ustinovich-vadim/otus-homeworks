<?php

namespace App\Services\User;

use App\DTO\UserRegisterDTO;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use stdClass;

readonly class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
        //
    }

    public function register(UserRegisterDTO $userRegisterDTO): void
    {
        $hashedPassword = Hash::make($userRegisterDTO->getPassword());

        $this->userRepository->create($userRegisterDTO, $hashedPassword);
    }

    public function findByEmail(string $email): ?stdClass
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findById(int $id): ?stdClass
    {
        return $this->userRepository->findById($id);
    }

    public function validatePassword(string $inputPassword, string $storedPassword): bool
    {
        return Hash::check($inputPassword, $storedPassword);
    }
}
