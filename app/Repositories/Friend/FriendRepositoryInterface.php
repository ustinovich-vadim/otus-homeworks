<?php

namespace App\Repositories\Friend;

interface FriendRepositoryInterface
{
    public function getFriendIds(int $userId): array;

    public function addFriend(int $userId, int $friendId): bool;

    public function deleteFriend(int $userId, int $friendId): bool;

    public function cacheFriendIdsForUser(int $userId): void;
}
