<?php

namespace App\Repositories\Message;

use App\Traits\TarantoolClientTrait;

class TarantoolMessageRepository implements MessageRepositoryInterface
{
    use TarantoolClientTrait;

    public function __construct()
    {
        $this->initTarantoolClient();
    }

    public function getMessagesFromDialog(int $dialogWithUserId, int $offset, int $limit): array
    {
        $result = $this->client->call('get_messages', $dialogWithUserId, $offset, $limit);

        return $result[0] ? array_map([$this, 'formatMessage'], $result[0]) : [];
    }

    public function createMessage(int $authUserId, int $dialogWithUserId, string $text): int
    {
        $result = $this->client->call('add_message', $dialogWithUserId, $authUserId, $text);

        return $result[0];
    }

    private function formatMessage(array $message): array
    {
        return [
            'id' => $message[0],
            'dialog_id' => $message[1],
            'sender_id' => $message[2],
            'text' => $message[3],
            'created_at' => date('Y-m-d H:i:s', $message[4]), // Преобразование временной метки в читаемый формат
            'updated_at' => date('Y-m-d H:i:s', $message[5]),
        ];
    }
}
