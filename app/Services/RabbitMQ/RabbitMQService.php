<?php

declare(strict_types=1);

namespace App\Services\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
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

    public function publishMessage(string $queueName, array $messageData): void
    {
        $this->initConnection();

        $this->channel->queue_declare($queueName, false, true, false, false);

        $message = new AMQPMessage(json_encode($messageData), [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $this->channel->basic_publish($message, '', $queueName);
    }

    public function __destruct()
    {
        $this->channel?->close();

        $this->connection?->close();
    }
}
