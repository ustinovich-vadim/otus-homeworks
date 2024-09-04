<?php

namespace App\Console\Commands;

use App\Services\Feed\FriendFeedService;
use Illuminate\Console\Command;
use App\Services\Friend\FriendService;
use App\Repositories\User\UserRepositoryInterface;

class WarmupCache extends Command
{
    protected $signature = 'cache:warmup';

    protected $description = 'Warm up the cache for users\' friends and their news feed';

    protected FriendService $friendService;
    protected UserRepositoryInterface $userRepository;
    protected FriendFeedService $friendFeedService;

    public function __construct(
        FriendService $friendService,
        UserRepositoryInterface $userRepository,
        FriendFeedService $friendFeedService
    ) {
        parent::__construct();

        $this->friendService = $friendService;
        $this->userRepository = $userRepository;
        $this->friendFeedService = $friendFeedService;
    }

    public function handle()
    {
        $this->info('Warming up cache...');

        $this->userRepository->getUsersChunked(1000, function ($users) {
            foreach ($users as $user) {
                $this->friendService->cacheFriendIdsForUser($user->id);
                $this->friendFeedService->cacheFeedForUser($user->id);
                gc_collect_cycles();
                usleep(100000);

                $this->info("Cache warmed for user: {$user->id}");
            }
        });

        $this->info('Cache warming completed.');
    }
}
