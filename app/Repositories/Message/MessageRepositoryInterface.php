<?php

namespace App\Repositories\Message;

interface MessageRepositoryInterface
{
    public function getMessagesFromDialog(int $dialogWithUserId, int $offset, int $limit): array;

    public function createMessage(int $authUserId, int $dialogWithUserId, string $text): int;
}
