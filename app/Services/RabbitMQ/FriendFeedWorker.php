<?php

namespace App\Services\RabbitMQ;

use App\Jobs\UpdateFriendFeedsJob;
use PhpAmqpLib\Message\AMQPMessage;

class FriendFeedWorker extends RabbitMQWorker
{
    protected function handleMessage(AMQPMessage $msg): void
    {
        $data = json_decode($msg->body, true);

        $job = unserialize($data['job']);

        if ($job instanceof UpdateFriendFeedsJob) {
            $job->handle();
        }

        $this->channel->basic_ack($msg->get('delivery_tag'));
    }
}
