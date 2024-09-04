<?php

namespace App\Console\Commands;

use App\Services\RabbitMQ\RabbitMQWorker;
use Illuminate\Console\Command;

class RabbitMQWorkerCommand extends Command
{
    protected $signature = 'rabbitmq:work';
    protected $description = 'Listen to RabbitMQ queue';

    protected RabbitMQWorker $worker;

    public function __construct(RabbitMQWorker $worker)
    {
        parent::__construct();

        $this->worker = $worker;
    }

    /**
     * @throws \ErrorException
     */
    public function handle(): void
    {
        sleep(10);

        $this->worker->initConnection();
        $this->worker->listenToQueue('update_friend_feeds_queue');
    }
}
