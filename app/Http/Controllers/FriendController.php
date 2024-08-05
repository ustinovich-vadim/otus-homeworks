<?php

namespace App\Http\Controllers;

use App\Http\Requests\Friend\AddFriendRequest;
use App\Http\Requests\Friend\DeleteFriendRequest;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Friend\FriendService;
use Symfony\Component\HttpFoundation\Response;

class FriendController extends Controller
{
    public function __construct(private readonly FriendService $friendService)
    {
        //
    }

    public function addFriend(AddFriendRequest $request): Response
    {
        $userId = AuthenticatedUser::getId();

        $friendId = $request->integer('friend_id');
        $success = $this->friendService->addFriend($userId, $friendId);

        if ($success) {
            return response()->json(['message' => 'Friend added successfully.']);
        } else {
            return response()->json(['message' => 'Failed to add friend.'], 500);
        }
    }

    public function deleteFriend(DeleteFriendRequest $request): Response
    {
        $userId = AuthenticatedUser::getId();
        $friendId = (int) $request->route('user_id');

        $success = $this->friendService->deleteFriend($userId, $friendId);

        if ($success) {
            return response()->json(['message' => 'Friend removed successfully.']);
        } else {
            return response()->json(['message' => 'Failed to remove friend.'], 500);
        }
    }
}
