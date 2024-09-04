<?php

namespace App\Providers;


use App\Services\RabbitMQ\FriendFeedWorker;
use App\Services\RabbitMQ\NotificationWorker;
use App\Services\RabbitMQ\RabbitMQService;
use Illuminate\Support\ServiceProvider;

class RabbitMQServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RabbitMQService::class, function ($app) {
            return new RabbitMQService(
                config('queue.connections.rabbitmq.host'),
                config('queue.connections.rabbitmq.port'),
                config('queue.connections.rabbitmq.user'),
                config('queue.connections.rabbitmq.password'),
                config('queue.connections.rabbitmq.vhost')
            );
        });

        $this->app->bind(FriendFeedWorker::class, function ($app) {
            return new FriendFeedWorker(
                config('queue.connections.rabbitmq.host'),
                config('queue.connections.rabbitmq.port'),
                config('queue.connections.rabbitmq.user'),
                config('queue.connections.rabbitmq.password'),
                config('queue.connections.rabbitmq.vhost')
            );
        });

        $this->app->bind(NotificationWorker::class, function ($app) {
            return new NotificationWorker(
                config('queue.connections.rabbitmq.host'),
                config('queue.connections.rabbitmq.port'),
                config('queue.connections.rabbitmq.user'),
                config('queue.connections.rabbitmq.password'),
                config('queue.connections.rabbitmq.vhost')
            );
        });
    }
}
