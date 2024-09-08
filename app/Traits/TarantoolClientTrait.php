<?php

namespace App\Traits;

use Tarantool\Client\Client;

trait TarantoolClientTrait
{
    protected Client $client;

    public function initTarantoolClient(): void
    {
        if (!isset($this->client)) {
            $this->client = Client::fromDsn('tcp://tarantool:3301');
        }
    }
}
