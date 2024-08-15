<?php

namespace App\Repositories\Dialog;

use Illuminate\Support\Facades\DB;

class DialogRepository implements DialogRepositoryInterface
{
    public function getOrCreateDialogWithUser(int $userId, int $dialogPartnerId): array
    {
        [$user1, $user2] = $this->normalizeUserIds($userId, $dialogPartnerId);

        $dialog = DB::table('dialogs')
            ->where('user_id_1', $user1)
            ->where('user_id_2', $user2)
            ->first();

        if ($dialog) {
            return (array) $dialog;
        }

        $dialogId = DB::table('dialogs')->insertGetId([
            'user_id_1' => $user1,
            'user_id_2' => $user2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (array) DB::table('dialogs')->where('id', $dialogId)->first();
    }

    public function getDialogIdWithUser(int $userId, int $dialogPartnerId): ?int
    {
        [$user1, $user2] = $this->normalizeUserIds($userId, $dialogPartnerId);

        return DB::table('dialogs')
            ->where('user_id_1', $user1)
            ->where('user_id_2', $user2)
            ->first()
            ->id ?? null;
    }

    private function normalizeUserIds(int $userId, int $dialogPartnerId): array
    {
        $user1 = min($userId, $dialogPartnerId);
        $user2 = max($userId, $dialogPartnerId);

        return [$user1, $user2];
    }
}
