<?php

namespace App\Repositories\Dialog;

interface DialogRepositoryInterface
{
    public function getDialogIdWithUser(int $userId, int $dialogPartnerId): ?int;

    public function getOrCreateDialogWithUser(int $userId, int $dialogPartnerId): array;
}
