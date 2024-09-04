<?php

namespace App\Services\Post;

use App\Repositories\Friend\FriendRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;

readonly class PostService
{
    public function __construct(
        private PostRepositoryInterface   $postRepository,
        private FriendRepositoryInterface $friendRepository
    ) {
        //
    }

    public function createPost(int $userId, string $text): int
    {
        return $this->postRepository->createPost($userId, $text);
    }

    public function updatePost(int $postId, string $text): void
    {
        $this->postRepository->updatePost($postId, $text);
    }

    public function deletePost(int $postId): void
    {
        $this->postRepository->deletePost($postId);
    }

    public function getPost(int $postId): array
    {
        return $this->postRepository->getPost($postId);
    }

    public function updateFriendFeedForNewPost(int $friendId, array $post): void
    {
        $this->postRepository->updateCacheForNewPost(
            friendId: $friendId,
            post: $post,
            relatedFriendIds: $this->friendRepository->getFriendIds($friendId)
        );
    }

    public function updateFriendFeedForUpdatedPost(int $friendId, array $post): void
    {
        $this->postRepository->updateCacheForUpdatedPost($friendId, $post);
    }

    public function updateCacheForDeletedPost(int $friendId): void
    {
        $this->postRepository->updateCacheForDeletedPost(
            friendId: $friendId,
            relatedFriendIds: $this->friendRepository->getFriendIds($friendId)
        );
    }

    public function getPostById(int $postId): array
    {
        return $this->postRepository->getPost($postId);
    }
}
