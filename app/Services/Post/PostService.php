<?php

namespace App\Services\Post;

use App\Jobs\UpdateFriendFeedsJob;
use App\Repositories\Friend\FriendRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Services\RabbitMQ\RabbitMQService;

readonly class PostService
{
    public function __construct(
        private PostRepositoryInterface   $postRepository,
        private FriendRepositoryInterface $friendRepository,
        private RabbitMQService $rabbitMQService
    ) {
        //
    }

    public function createPost(int $userId, string $text): int
    {
        $postId = $this->postRepository->createPost($userId, $text);
        $friendIds = $this->friendRepository->getFriendIds($userId);

        $this->dispatchUpdateFriendFeedsJobToQueue(UpdateFriendFeedsJob::EVENT_CREATED, $postId, $friendIds);

        return $postId;
    }

    public function updatePost(int $postId, string $text): void
    {
        $this->postRepository->updatePost($postId, $text);
        $post = $this->postRepository->getPost($postId);
        $friendIds = $this->friendRepository->getFriendIds($post['user_id']);

        $this->dispatchUpdateFriendFeedsJobToQueue(UpdateFriendFeedsJob::EVENT_UPDATED, $postId, $friendIds);
    }

    public function deletePost(int $postId): void
    {
        $post = $this->postRepository->getPost($postId);
        $this->postRepository->deletePost($postId);
        $friendIds = $this->friendRepository->getFriendIds($post['user_id']);

        $this->dispatchUpdateFriendFeedsJobToQueue(UpdateFriendFeedsJob::EVENT_DELETED, $postId, $friendIds);
    }

    public function getPost(int $postId): array
    {
        return $this->postRepository->getPost($postId);
    }

    public function getFriendFeed(int $userId, int $offset, int $limit): array
    {
        $friendIds = $this->friendRepository->getFriendIds($userId);

        return $this->postRepository->getPostsByUserIdAndFriendIds($userId, $friendIds, $offset, $limit);
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

    public function cacheFeedForUser(int $userId): void
    {
        $friendIds = $this->friendRepository->getFriendIds($userId);
        $this->postRepository->regenerateCacheForUser($userId, $friendIds);
    }

    private function dispatchUpdateFriendFeedsJobToQueue(string $eventType, int $postId, array $friendIds): void
    {
        $job = new UpdateFriendFeedsJob($eventType, $postId, $friendIds);
        $serializedJob = serialize($job);
        $this->rabbitMQService->publishMessage('update_friend_feeds_queue', ['job' => $serializedJob]);
    }
}
