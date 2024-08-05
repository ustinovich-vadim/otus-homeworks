<?php

namespace App\Jobs;

use App\Services\Post\PostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateFriendFeedsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const EVENT_CREATED = 'created';
    public const EVENT_UPDATED = 'updated';
    public const EVENT_DELETED = 'deleted';

    private string $eventType;
    private int $postId;
    private array $friendIds;

    public function __construct(string $eventType, int $postId, array $friendIds)
    {
        $this->eventType = $eventType;
        $this->postId = $postId;
        $this->friendIds = $friendIds;
    }

    public function handle(PostService $postService): void
    {
        match ($this->eventType) {
            self::EVENT_CREATED => $this->handlePostCreated($postService),
            self::EVENT_UPDATED => $this->handlePostUpdated($postService),
            self::EVENT_DELETED => $this->handlePostDeleted($postService),
            default => null,
        };
    }

    private function handlePostCreated(PostService $postService): void
    {
        $post = $postService->getPostById($this->postId);

        if (!$post) {
            return;
        }

        foreach ($this->friendIds as $userId) {
            $postService->updateFriendFeedForNewPost(friendId: $userId, post: $post);
        }
    }

    private function handlePostUpdated(PostService $postService): void
    {
        $post = $postService->getPostById($this->postId);

        if (!$post) {
            return;
        }

        foreach ($this->friendIds as $friendId) {
            $postService->updateFriendFeedForUpdatedPost(friendId: $friendId, post: $post);
        }
    }

    private function handlePostDeleted(PostService $postService): void
    {
        foreach ($this->friendIds as $friendId) {
            $postService->updateCacheForDeletedPost($friendId);
        }
    }
}
