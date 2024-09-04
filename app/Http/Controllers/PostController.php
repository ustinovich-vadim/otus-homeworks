<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Requests\Post\GetPostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Jobs\UpdateFriendFeedsJob;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Feed\FriendFeedService;
use App\Services\Post\PostService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function __construct(protected PostService $postService, private FriendFeedService $friendFeedService)
    {
        //
    }

    public function create(CreatePostRequest $request): JsonResponse
    {
        $userId = AuthenticatedUser::getId();
        $text = $request->string('text');

        $postId = $this->postService->createPost($userId, $text);

        $this->friendFeedService->updateFriendFeed(
            eventType: UpdateFriendFeedsJob::EVENT_CREATED,
            userId: $userId,
            postId: $postId
        );

        return response()->json(['id' => $postId], Response::HTTP_OK);
    }

    public function update(UpdatePostRequest $request): JsonResponse
    {
        $postId = (int) $request->route('post_id');
        $text = $request->input('text');

        $this->postService->updatePost($postId, $text);
        $this->friendFeedService->updateFriendFeed(
            eventType: UpdateFriendFeedsJob::EVENT_UPDATED,
            userId: null,
            postId: $postId
        );

        return response()->json(['message' => 'Post updated successfully'], Response::HTTP_OK);
    }

    public function delete(DeletePostRequest $request): JsonResponse
    {
        $postId = $request->route('post_id');

        $this->postService->deletePost($postId);
        $this->friendFeedService->updateFriendFeed(
            eventType: UpdateFriendFeedsJob::EVENT_DELETED,
            userId: null,
            postId: $postId
        );

        return response()->json(['message' => 'Post deleted successfully'], Response::HTTP_OK);
    }

    public function get(GetPostRequest $request): JsonResponse
    {
        $postId = (int) $request->route('post_id');

        $post = $this->postService->getPost($postId);

        return response()->json($post, Response::HTTP_OK);
    }
}
