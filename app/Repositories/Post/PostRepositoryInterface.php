<?php

namespace App\Repositories\Post;

interface PostRepositoryInterface
{
    public function getPostsByUserIdAndFriendIds(int $userId, array $friendIds, int $offset = 0, int $limit = 10): array;

    public function createPost(int $userId, string $text): int;

    public function updatePost(int $postId, string $text): void;

    public function deletePost(int $postId): void;

    public function getPost(int $postId): array;

    public function updateCacheForNewPost(int $friendId, array $post, array $relatedFriendIds): void;

    public function updateCacheForUpdatedPost(int $friendId, array $post): void;

    public function updateCacheForDeletedPost(int $friendId, array $relatedFriendIds): void;

    public function regenerateCacheForUser(int $userId, array $relatedFriendIds): void;
}
