<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Friend\FriendService;
use App\Services\Post\PostService;
use App\Repositories\User\UserRepositoryInterface;

class WarmupCache extends Command
{
    protected $signature = 'cache:warmup';

    protected $description = 'Warm up the cache for users\' friends and their news feed';

    protected FriendService $friendService;
    protected PostService $postService;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        FriendService $friendService,
        PostService $postService,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct();

        $this->friendService = $friendService;
        $this->postService = $postService;
        $this->userRepository = $userRepository;
    }

    public function handle()
    {
        $this->info('Warming up cache...');

        $this->userRepository->getUsersChunked(1000, function ($users) {
            foreach ($users as $user) {
                $this->friendService->cacheFriendIdsForUser($user->id);
                $this->postService->cacheFeedForUser($user->id);
                gc_collect_cycles();
                usleep(100000);

                $this->info("Cache warmed for user: {$user->id}");
            }
        });

        $this->info('Cache warming completed.');
    }
}
