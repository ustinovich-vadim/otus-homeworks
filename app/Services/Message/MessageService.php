<?php

namespace App\Services\Message;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class MessageService
{
    protected string $baseUrlForMicroservice;

    public function __construct()
    {
        $this->baseUrlForMicroservice = config('microservices.dialog_microservice.base_uri');
    }

    /**
     * @throws ConnectionException
     */
    public function getMessages($userId, $authHeaderValue, $requestIdHeaderValue)
    {
        $url = $this->baseUrlForMicroservice . "/messages/{$userId}/list";
        $headers = $this->prepareHeaders($authHeaderValue, $requestIdHeaderValue);

        return Http::withHeaders($headers)->get($url)->json();
    }

    /**
     * @throws ConnectionException
     */
    public function createMessage($userId, $data, $authHeaderValue, $requestIdHeaderValue)
    {
        $url = $this->baseUrlForMicroservice . "/messages/{$userId}/send";
        $headers = $this->prepareHeaders($authHeaderValue, $requestIdHeaderValue);

        return Http::withHeaders($headers)->post($url, $data)->json();
    }

    private function prepareHeaders(string $authHeaderValue, string $requestIdHeaderValue): array
    {
        return [
            'Authorization' => $authHeaderValue,
            'x-request-id' => $requestIdHeaderValue,
            'Content-Type' => 'application/json',
        ];
    }
}

