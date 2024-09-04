<?php

namespace App\Jobs;

use App\Services\Post\PostService;

class UpdateFriendFeedsJob
{
    public const EVENT_CREATED = 'created';
    public const EVENT_UPDATED = 'updated';
    public const EVENT_DELETED = 'deleted';

    private string $eventType;
    private int $postId;
    private array $friendIds;
    private PostService $postService;

    public function __construct(string $eventType, int $postId, array $friendIds)
    {
        $this->eventType = $eventType;
        $this->postId = $postId;
        $this->friendIds = $friendIds;
        $this->postService = app(PostService::class);
    }

    public function handle(): void
    {
        match ($this->eventType) {
            self::EVENT_CREATED => $this->handlePostCreated(),
            self::EVENT_UPDATED => $this->handlePostUpdated(),
            self::EVENT_DELETED => $this->handlePostDeleted(),
            default => null,
        };

        dump('Job handled successful');
    }

    private function handlePostCreated(): void
    {
        $post = $this->postService->getPostById($this->postId);

        if (!$post) {
            return;
        }

        foreach ($this->friendIds as $userId) {
            $this->postService->updateFriendFeedForNewPost(friendId: $userId, post: $post);
        }
    }

    private function handlePostUpdated(): void
    {
        $post = $this->postService->getPostById($this->postId);

        if (!$post) {
            return;
        }

        foreach ($this->friendIds as $friendId) {
            $this->postService->updateFriendFeedForUpdatedPost(friendId: $friendId, post: $post);
        }
    }

    private function handlePostDeleted(): void
    {
        foreach ($this->friendIds as $friendId) {
            $this->postService->updateCacheForDeletedPost($friendId);
        }
    }
}
