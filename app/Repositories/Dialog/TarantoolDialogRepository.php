<?php

namespace App\Repositories\Dialog;

use App\Traits\TarantoolClientTrait;

class TarantoolDialogRepository implements DialogRepositoryInterface
{
    use TarantoolClientTrait;

    public function __construct()
    {
        $this->initTarantoolClient();
    }

    public function getOrCreateDialogWithUser(int $userId, int $dialogPartnerId): array
    {
        [$user1, $user2] = $this->normalizeUserIds($userId, $dialogPartnerId);

        $result = $this->client->call('get_or_create_dialog', $user1, $user2);

        return $result[0] ? $this->formatTarantoolResult($result[0]) : [];
    }

    public function getDialogIdWithUser(int $userId, int $dialogPartnerId): ?int
    {
        [$user1, $user2] = $this->normalizeUserIds($userId, $dialogPartnerId);

        $result = $this->client->call('get_dialog_id', $user1, $user2);

        return $result[0] ?? null;
    }

    private function normalizeUserIds(int $userId, int $dialogPartnerId): array
    {
        $user1 = min($userId, $dialogPartnerId);
        $user2 = max($userId, $dialogPartnerId);

        return [$user1, $user2];
    }

    private function formatTarantoolResult(array $tarantoolResult): array
    {
        return [
            'id' => $tarantoolResult[0],
            'user_id_1' => $tarantoolResult[1],
            'user_id_2' => $tarantoolResult[2],
            'created_at' => date('Y-m-d H:i:s', $tarantoolResult[3]),
            'updated_at' => date('Y-m-d H:i:s', $tarantoolResult[4]),
        ];
    }
}
