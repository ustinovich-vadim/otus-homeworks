<?php

namespace App\Providers;


use App\Services\RabbitMQ\RabbitMQService;
use App\Services\RabbitMQ\RabbitMQWorker;
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

        $this->app->bind(RabbitMQWorker::class, function ($app) {
            return new RabbitMQWorker(
                config('queue.connections.rabbitmq.host'),
                config('queue.connections.rabbitmq.port'),
                config('queue.connections.rabbitmq.user'),
                config('queue.connections.rabbitmq.password'),
                config('queue.connections.rabbitmq.vhost')
            );
        });
    }
}
