<?php

namespace App\Services\RabbitMQ;

use App\Enums\PostEventTypeEnum;
use App\Events\PostCreated;
use App\Events\PostDeleted;
use App\Events\PostUpdated;
use PhpAmqpLib\Message\AMQPMessage;

class NotificationWorker extends RabbitMQWorker
{
    protected function handleMessage(AMQPMessage $msg): void
    {
        $data = json_decode($msg->body, true);

        $this->sendWebSocketNotification($data);

        $this->channel->basic_ack($msg->get('delivery_tag'));
    }

    private function sendWebSocketNotification(array $data): void
    {
        match ($data['event_type']) {
            PostEventTypeEnum::POST_CREATED->value => event(new PostCreated($data['post'], $data['friends'])),
            PostEventTypeEnum::POST_UPDATED->value => event(new PostUpdated($data['post'], $data['friends'])),
            PostEventTypeEnum::POST_DELETED->value => event(new PostDeleted($data['post_id'], $data['friends'])),
        };
    }
}
