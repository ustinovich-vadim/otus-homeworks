<?php

namespace App\Console\Commands;

use App\Services\RabbitMQ\FriendFeedWorker;
use Illuminate\Console\Command;

class RabbitMQFriendFeedWorkerCommand extends Command
{
    public const QUEUE_NAME = 'regular_posts_queue';
    protected $signature = 'rabbitmq:friend-feed-work';
    protected $description = 'Listen to RabbitMQ friend feed queue';

    protected FriendFeedWorker $worker;

    public function __construct(FriendFeedWorker $worker)
    {
        parent::__construct();
        $this->worker = $worker;
    }

    public function handle(): void
    {
        sleep(10);
        $this->worker->initConnection();
        $this->worker->listenToQueue(self::QUEUE_NAME);
    }
}
