<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\GetMessagesRequest;
use App\Services\Message\MessageService;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function __construct(private readonly MessageService $messageService)
    {
        //
    }

    /**
     * @throws ConnectionException
     */
    public function index(GetMessagesRequest $request): Response
    {
        $responseData =  $this->messageService->getMessages(
            userId: $request->route('user_id'),
            authHeaderValue: $request->header('Authorization'),
            requestIdHeaderValue: $request->header('x-request-id')
        );

        return response()->json($responseData);
    }

    public function create(CreateMessageRequest $request): Response
    {
        try {
            $responseData =  $this->messageService->createMessage(
                userId: $request->route('user_id'),
                data: $request->all(),
                authHeaderValue: $request->header('Authorization'),
                requestIdHeaderValue: $request->header('x-request-id')
            );

            return response()->json($responseData);
        } catch (Exception $e) {
            return response()->json('Failed to create message', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
