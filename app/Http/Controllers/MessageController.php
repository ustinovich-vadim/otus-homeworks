<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\GetMessagesRequest;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Message\MessageService;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function __construct(private readonly MessageService $messageService)
    {
        //
    }

    public function index(GetMessagesRequest $request): Response
    {
        $authUserId = AuthenticatedUser::getId();
        $dialogPartnerId = $request->route('user_id');
        $offset = $request->integer('offset', 0);
        $limit = $request->integer('limit', 100);

        $messages = $this->messageService->getMessages(
            authUserId: $authUserId,
            dialogPartnerId: $dialogPartnerId,
            offset: $offset,
            limit: $limit
        );

        return response()->json($messages, Response::HTTP_OK);
    }

    public function create(CreateMessageRequest $request): Response
    {
        $authUserId = AuthenticatedUser::getId();
        $dialogPartnerId = $request->route('user_id');
        $text = $request->string('text');

        try {
             $this->messageService->createMessage(
                authUserId: $authUserId,
                dialogPartnerId: $dialogPartnerId,
                text: $text
            );

            return response()->json('Message created successfully', Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json('Failed to create message', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
