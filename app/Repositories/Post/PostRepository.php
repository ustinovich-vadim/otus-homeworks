<?php

namespace App\Repositories\Post;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PostRepository implements PostRepositoryInterface
{
    private const CACHE_KEY_PREFIX = 'posts_feed_';
    private const CACHE_EXPIRATION_MINUTES = 60;
    private const MAX_POSTS_IN_CACHE = 1000;

    public function createPost(int $userId, string $text): int
    {
        return DB::table('posts')->insertGetId([
            'user_id' => $userId,
            'content' => $text,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updatePost(int $postId, string $text): void
    {
        DB::table('posts')
            ->where('id', $postId)
            ->update(['content' => $text, 'updated_at' => now()]);
    }

    public function deletePost(int $postId): void
    {
        DB::table('posts')
            ->where('id', $postId)
            ->delete();
    }

    public function getPost(int $postId): array
    {
        return (array) DB::table('posts')
            ->where('id', $postId)
            ->first();
    }

    public function getPostsByUserIdAndFriendIds(int $userId, array $friendIds, int $offset = 0, int $limit = 10): array
    {
        $cacheKey = $this->generateCacheKey($userId);

        $posts = Cache::remember($cacheKey, self::CACHE_EXPIRATION_MINUTES, function () use ($friendIds) {
            return $this->getPostsFromDB($friendIds);
        });

        return array_slice($posts, $offset, $limit);
    }

    public function updateCacheForNewPost(int $friendId, array $post, array $relatedFriendIds): void
    {
        $cacheKey = $this->generateCacheKey($friendId);

        $posts = Cache::get($cacheKey);
        if ($posts !== null) {
            array_unshift($posts, $post);

            if (count($posts) > self::MAX_POSTS_IN_CACHE) {
                $posts = array_slice($posts, 0, self::MAX_POSTS_IN_CACHE);
            }

            Cache::put($cacheKey, $posts, self::CACHE_EXPIRATION_MINUTES);
        } else {
            Cache::put($cacheKey, $this->getPostsFromDB($relatedFriendIds), self::CACHE_EXPIRATION_MINUTES);
        }
    }

    public function updateCacheForUpdatedPost(int $friendId, array $post): void
    {
        $cacheKey = $this->generateCacheKey($friendId);

        $posts = Cache::get($cacheKey);

        if ($posts !== null) {
            foreach ($posts as &$cachedPost) {
                if ($cachedPost->id === $post['id']) {
                    $cachedPost = (object) $post;
                    break;
                }
            }

            Cache::put($cacheKey, $posts, self::CACHE_EXPIRATION_MINUTES);
        }
    }

    public function updateCacheForDeletedPost(int $friendId, array $relatedFriendIds): void
    {
        $cacheKey = $this->generateCacheKey($friendId);

        $posts = $this->getPostsFromDB($relatedFriendIds);

        Cache::put($cacheKey, $posts, self::CACHE_EXPIRATION_MINUTES);
    }

    public function regenerateCacheForUser(int $userId, array $relatedFriendIds): void
    {
        $cacheKey = $this->generateCacheKey($userId);
        $posts = $this->getPostsFromDB($relatedFriendIds);

        Cache::put($cacheKey, $posts, self::CACHE_EXPIRATION_MINUTES);
    }

    private function getPostsFromDB(array $friendIds): array
    {
        return DB::table('posts')
            ->whereIn('user_id', $friendIds)
            ->orderBy('created_at', 'desc')
            ->limit(self::MAX_POSTS_IN_CACHE)
            ->get()
            ->toArray();
    }

    private function generateCacheKey(int $userId): string
    {
        return self::CACHE_KEY_PREFIX . $userId;
    }
}
