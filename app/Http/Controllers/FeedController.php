<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\FeedRequest;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Feed\FriendFeedService;
use Symfony\Component\HttpFoundation\Response;

class FeedController
{
    public function __construct(private FriendFeedService $friendFeedService)
    {
        //
    }

    public function feed(FeedRequest $request): Response
    {
        $userId = AuthenticatedUser::getId();
        $offset = $request->integer('offset', 0);
        $limit = $request->integer('limit', 10);

        $feed = $this->friendFeedService->getFriendFeed($userId, $offset, $limit);

        return response()->json($feed);
    }
}
