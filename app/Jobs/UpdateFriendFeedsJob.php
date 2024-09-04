<?php

namespace App\Jobs;

use App\Console\Commands\RabbitMQNotificationWorkerCommand;
use App\Enums\PostEventTypeEnum;
use App\Services\Post\PostService;
use App\Services\RabbitMQ\RabbitMQService;

class UpdateFriendFeedsJob
{
    public const EVENT_CREATED = 'created';
    public const EVENT_UPDATED = 'updated';
    public const EVENT_DELETED = 'deleted';

    private string $eventType;
    private int $postId;
    private array $friendIds;
    private PostService $postService;
    private RabbitMQService $rabbitMQService;

    public function __construct(string $eventType, int $postId, array $friendIds)
    {
        $this->eventType = $eventType;
        $this->postId = $postId;
        $this->friendIds = $friendIds;
        $this->postService = app(PostService::class);
        $this->rabbitMQService = app(RabbitMQService::class);
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

        $this->rabbitMQService->publishMessage(
            RabbitMQNotificationWorkerCommand::QUEUE_NAME,
            [
                'post' => $post,
                'friends' => $this->friendIds,
                'event_type' => PostEventTypeEnum::POST_CREATED->value,
            ]
        );
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

        $this->rabbitMQService->publishMessage(
            RabbitMQNotificationWorkerCommand::QUEUE_NAME,
            [
                'post' => $post,
                'friends' => $this->friendIds,
                'event_type' => PostEventTypeEnum::POST_UPDATED->value,
            ]
        );
    }

    private function handlePostDeleted(): void
    {
        foreach ($this->friendIds as $friendId) {
            $this->postService->updateCacheForDeletedPost($friendId);
        }

        $this->rabbitMQService->publishMessage(
            RabbitMQNotificationWorkerCommand::QUEUE_NAME,
            [
                'post_id' => $this->postId,
                'friends' => $this->friendIds,
                'event_type' => PostEventTypeEnum::POST_DELETED->value,
            ]
        );
    }
}
