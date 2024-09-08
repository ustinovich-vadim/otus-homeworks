<?php

namespace App\Services\Message;

use App\Repositories\Message\MessageRepositoryInterface;
use App\Services\Dialog\DialogService;
use Illuminate\Support\Facades\DB;

readonly class MessageService
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private DialogService $dialogService
    ) {
        //
    }

    public function getMessages(int $authUserId, int $dialogPartnerId, int $offset, int $limit): array
    {

        $dialogWithUser = $this->dialogService->getDialogIdWithUser($authUserId, $dialogPartnerId);

        return $dialogWithUser ? $this->messageRepository->getMessagesFromDialog($dialogWithUser, $offset, $limit) : [];
    }

    public function createMessage($authUserId, $dialogPartnerId, $text): void
    {
        DB::transaction(function () use ($authUserId, $dialogPartnerId, $text) {
            $dialogWithUser = $this->dialogService->getOrCreateDialogIdWithUser($authUserId, $dialogPartnerId);

            $this->messageRepository->createMessage($authUserId, $dialogWithUser['id'], $text);
        });
    }
}
