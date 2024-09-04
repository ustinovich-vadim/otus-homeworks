<?php

declare(strict_types=1);

namespace App\Services\RabbitMQ;

use App\Jobs\UpdateFriendFeedsJob;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQWorker
{
    protected ?AMQPStreamConnection $connection = null;
    protected ?AMQPChannel $channel = null;

    protected string $host;
    protected int $port;
    protected string $user;
    protected string $password;
    protected string $vhost;

    public function __construct(string $host, int $port, string $user, string $password, string $vhost = '/')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    public function initConnection(): void
    {
        if ($this->connection === null) {
            $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password, $this->vhost);
            $this->channel = $this->connection->channel();
        }
    }

    /**
     * @throws \ErrorException
     */
    public function listenToQueue(string $queueName): void
    {
        $this->initConnection();

        $this->channel->queue_declare($queueName, false, true, false, false);

        $callback = function (AMQPMessage $msg) {
            $this->handleMessage($msg);
        };

        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    private function handleMessage(AMQPMessage $msg): void
    {
        $data = json_decode($msg->body, true);

        $job = unserialize($data['job']);

        if ($job instanceof UpdateFriendFeedsJob) {
            $job->handle();
        }

        $this->channel->basic_ack($msg->get('delivery_tag'));
    }

    public function __destruct()
    {
        $this->channel?->close();
        $this->connection?->close();
    }
}
