<?php

namespace App\Services\Friend;

use App\Repositories\Friend\FriendRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;

readonly class FriendService
{
    public function __construct(
        private FriendRepositoryInterface $friendRepository,
        private PostRepositoryInterface   $postRepository
    ) {
        //
    }

    public function addFriend(int $userId, int $friendId): bool
    {
        $inserted = $this->friendRepository->addFriend($userId, $friendId);

        if ($inserted) {
            $this->updateCacheForUserAndNewFriend($userId, $friendId);
        }

        return $inserted;
    }

    public function deleteFriend(int $userId, int $friendId): bool
    {
        $deleted = $this->friendRepository->deleteFriend($userId, $friendId);

        if ($deleted) {
            $this->updateCacheForUserAndNewFriend($userId, $friendId);
        }

        return $deleted;
    }

    private function updateCacheForUserAndNewFriend(int $userId, int $friendId): void
    {
        $userFriends = $this->friendRepository->getFriendIds($userId);
        $this->postRepository->regenerateCacheForUser($userId, $userFriends);
        $friendsForNewFriend = $this->friendRepository->getFriendIds($friendId);
        $this->postRepository->regenerateCacheForUser($friendId, $friendsForNewFriend);
    }
}
