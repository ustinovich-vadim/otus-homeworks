<?php

namespace App\Console\Commands;

use App\Services\RabbitMQ\NotificationWorker;
use Illuminate\Console\Command;

class RabbitMQNotificationWorkerCommand extends Command
{
    public const QUEUE_NAME = 'notification_queue';
    protected $signature = 'rabbitmq:notification-work';
    protected $description = 'Listen to RabbitMQ notification queue';

    protected NotificationWorker $worker;

    public function __construct(NotificationWorker $worker)
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
