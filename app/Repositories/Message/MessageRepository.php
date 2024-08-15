<?php

namespace App\Repositories\Message;

use Illuminate\Support\Facades\DB;

class MessageRepository implements MessageRepositoryInterface
{
    public function getMessagesFromDialog(int $dialogWithUserId, int $offset, int $limit): array
    {
        return DB::table('messages')
            ->where('dialog_id', $dialogWithUserId)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function createMessage(int $authUserId, int $dialogWithUserId, string $text): int
    {
        return DB::table('messages')->insertGetId([
            'dialog_id' => $dialogWithUserId,
            'sender_id' => $authUserId,
            'text' => $text,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
