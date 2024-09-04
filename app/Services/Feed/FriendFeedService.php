<?php

namespace App\Services\Feed;

use App\Console\Commands\RabbitMQFriendFeedForCelebrityWorkerCommand;
use App\Console\Commands\RabbitMQFriendFeedWorkerCommand;
use App\Jobs\UpdateFriendFeedsJob;
use App\Repositories\Friend\FriendRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Services\RabbitMQ\RabbitMQService;

readonly class FriendFeedService
{
    public const CELEBRITY_FRIEND_COUNT = 10000;

    public function __construct(
        private PostRepositoryInterface   $postRepository,
        private FriendRepositoryInterface $friendRepository,
        private RabbitMQService           $rabbitMQService
    ) {
        //
    }

    public function getFriendFeed(int $userId, int $offset, int $limit): array
    {
        $friendIds = $this->friendRepository->getFriendIds($userId);

        return $this->postRepository->getPostsByUserIdAndFriendIds($userId, $friendIds, $offset, $limit);
    }

    public function updateFriendFeed(string $eventType, ?int $userId, int $postId): void
    {
        if(is_null($userId)) {
            $userId = $this->postRepository->getPost($postId)['user_id'];
        }

        $friendIds = $this->friendRepository->getFriendIds($userId);
        $queueName = count($friendIds) > self::CELEBRITY_FRIEND_COUNT
            ? RabbitMQFriendFeedForCelebrityWorkerCommand::QUEUE_NAME
            : RabbitMQFriendFeedWorkerCommand::QUEUE_NAME;

        $this->dispatchUpdateFriendFeedsJobToQueue($eventType, $postId, $friendIds, $queueName);
    }

    public function cacheFeedForUser(int $userId): void
    {
        $friendIds = $this->friendRepository->getFriendIds($userId);
        $this->postRepository->regenerateCacheForUser($userId, $friendIds);
    }

    private function dispatchUpdateFriendFeedsJobToQueue(
        string $eventType,
        int $postId,
        array $friendIds,
        string $queueName
    ): void {
        $job = new UpdateFriendFeedsJob($eventType, $postId, $friendIds);
        $serializedJob = serialize($job);
        $this->rabbitMQService->publishMessage($queueName, ['job' => $serializedJob]);
    }
}
