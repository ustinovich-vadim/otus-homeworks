<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $post;
    public array $friendIds;

    public function __construct(array $post, array $friendIds)
    {
        $this->post = $post;
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
            'postId' => $this->post['id'],
            'postText' => $this->post['content'],
            'author_user_id' => $this->post['user_id'],
        ];
    }
}
