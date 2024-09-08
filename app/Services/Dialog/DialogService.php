<?php

namespace App\Services\Dialog;

use App\Repositories\Dialog\DialogRepositoryInterface;

readonly class DialogService
{
    public function __construct(private DialogRepositoryInterface $dialogRepository)
    {
        //
    }

    public function getDialogIdWithUser(int $userId, int $dialogPartnerId): ?int
    {
        return $this->dialogRepository->getDialogIdWithUser($userId, $dialogPartnerId);
    }

    public function getOrCreateDialogIdWithUser(int $userId, int $dialogPartnerId): array
    {
        return $this->dialogRepository->getOrCreateDialogWithUser($userId, $dialogPartnerId);
    }
}
