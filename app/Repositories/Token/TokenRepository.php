<?php

namespace App\Repositories\Token;

use Illuminate\Support\Facades\DB;
use StdClass;

class TokenRepository implements TokenRepositoryInterface
{

    public function createToken(int $userId, string $token): void
    {
        DB::insert('INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())', [
            'App\Models\User',
            $userId,
            'auth_token',
            hash('sha256', $token),
            json_encode(['*']),
        ]);
    }

    public function getToken(string $token): ?StdClass
    {
        $tokenRecord = DB::select('SELECT * FROM personal_access_tokens WHERE token = ?', [$token]);

        return !empty($tokenRecord) ? $tokenRecord[0] : null;
    }
}
