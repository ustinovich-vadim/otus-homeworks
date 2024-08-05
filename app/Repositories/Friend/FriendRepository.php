<?php

namespace App\Repositories\Friend;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FriendRepository implements FriendRepositoryInterface
{
    private const CACHE_KEY_PREFIX = 'friend_ids_for_user_';
    private const CACHE_EXPIRATION_MINUTES = 60;

    public function addFriend(int $userId, int $friendId): bool
    {
        $inserted = DB::table('friends')->insert([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted) {
            $this->updateCacheForUser($userId);
        }

        return $inserted;
    }

    public function deleteFriend(int $userId, int $friendId): bool
    {
        $deleted = DB::table('friends')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->delete();

        if ($deleted) {
            $this->updateCacheForUser($userId);
        }

        return $deleted;
    }

    public function getFriendIds(int $userId): array
    {
        $cacheKey = $this->generateCacheKey($userId);

        return Cache::remember($cacheKey, self::CACHE_EXPIRATION_MINUTES, function () use ($userId) {
            return $this->getFiendsFormDb($userId);
        });
    }

    private function getFiendsFormDb(int $userId): array
    {
        return DB::table('friends')
            ->where('user_id', $userId)
            ->pluck('friend_id')
            ->toArray();
    }

    private function updateCacheForUser(int $userId): void
    {
        $friendIds = DB::table('friends')
            ->where('user_id', $userId)
            ->pluck('friend_id')
            ->toArray();

        $cacheKey = $this->generateCacheKey($userId);

        Cache::put($cacheKey, $friendIds, self::CACHE_EXPIRATION_MINUTES);
    }

    private function generateCacheKey(int $userId): string
    {
        return self::CACHE_KEY_PREFIX . $userId;
    }
}
