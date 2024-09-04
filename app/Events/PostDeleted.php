<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $postId;
    public array $friendIds;

    public function __construct(int $postId, array $friendIds)
    {
        $this->postId = $postId;
        $this->friendIds = $friendIds;
    }

    public function broadcastOn(): array
    {
        return array_map(function ($friendId) {
            return new PrivateChannel('App.Models.User.' . $friendId);
        }, $this->friendIds);
    }


    public function broadcastWith(): array
    {
        return [
            'postId' => $this->postId,
        ];
    }
}
