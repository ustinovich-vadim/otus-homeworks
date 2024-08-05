<?php

namespace App\Repositories\User;

use App\DTO\UserRegisterDTO;
use Illuminate\Support\Facades\DB;
use stdClass;

class UserRepository implements UserRepositoryInterface
{
    public function create(UserRegisterDTO $userRegisterDTO, string $hashedPassword): void
    {
        DB::insert('INSERT INTO users (name, surname, birth_date, gender, hobbies, city, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', [
            $userRegisterDTO->getName(),
            $userRegisterDTO->getSurname(),
            $userRegisterDTO->getBirthDate(),
            $userRegisterDTO->getGender(),
            $userRegisterDTO->getHobbies(),
            $userRegisterDTO->getCity(),
            $userRegisterDTO->getEmail(),
            $hashedPassword
        ]);
    }

    public function findByEmail(string $email): ?stdClass
    {
        $users = DB::select('SELECT * FROM users WHERE email = ?', [$email]);

        return !empty($users) ? $users[0] : null;
    }

    public function findById(int $id): ?stdClass
    {
        $users = DB::select('SELECT * FROM users WHERE id = ?', [$id]);

        return !empty($users) ? $users[0] : null;
    }

    public function searchByFirstAndLastName(string $name, string $surname): array
    {
        return DB::select('SELECT * FROM users WHERE name LIKE ? AND surname LIKE ? ORDER BY id', [$name . '%', $surname . '%']);
    }

    public function getUsersChunked(int $chunkSize, callable $callback): void
    {
        DB::table('users')->orderBy('id')->chunk($chunkSize, $callback);
    }
}
