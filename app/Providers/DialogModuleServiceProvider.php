<?php

namespace App\Providers;

use App\Repositories\Dialog\DialogRepository;
use App\Repositories\Dialog\DialogRepositoryInterface;
use App\Repositories\Dialog\TarantoolDialogRepository;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Message\TarantoolMessageRepository;
use Illuminate\Support\ServiceProvider;

class DialogModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $database = config('database.dialog_module_db');

        if ($database === 'postgres') {
            $this->app->bind(DialogRepositoryInterface::class, DialogRepository::class);
            $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        } elseif ($database === 'tarantool') {
            $this->app->bind(DialogRepositoryInterface::class, TarantoolDialogRepository::class);
            $this->app->bind(MessageRepositoryInterface::class, TarantoolMessageRepository::class);
        } else {
            throw new \Exception("Unsupported database for dialog module: {$database}");
        }
    }
}
