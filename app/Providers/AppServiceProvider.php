<?php

namespace App\Providers;

use App\Repositories\Dialog\DialogRepository;
use App\Repositories\Dialog\DialogRepositoryInterface;
use App\Repositories\Friend\FriendRepository;
use App\Repositories\Friend\FriendRepositoryInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Post\PostRepository;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\Token\TokenRepository;
use App\Repositories\Token\TokenRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Auth\AuthenticatedUser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(PostRepositoryInterface::class, PostRepository::class);
        $this->app->singleton(FriendRepositoryInterface::class, FriendRepository::class);
        $this->app->singleton(AuthenticatedUser::class, AuthenticatedUser::class);

        $this->app->bind(TokenRepositoryInterface::class, TokenRepository::class);
        $this->app->bind(DialogRepositoryInterface::class, DialogRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
