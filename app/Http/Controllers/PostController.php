<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Requests\Post\FeedRequest;
use App\Http\Requests\Post\GetPostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Post\PostService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function __construct(protected PostService $postService)
    {
        //
    }

    public function create(CreatePostRequest $request): JsonResponse
    {
        $userId = AuthenticatedUser::getId();
        $text = $request->string('text');

        $postId = $this->postService->createPost($userId, $text);

        return response()->json(['id' => $postId], Response::HTTP_OK);
    }

    public function update(UpdatePostRequest $request): JsonResponse
    {
        $postId = (int) $request->route('post_id');
        $text = $request->input('text');

        $this->postService->updatePost($postId, $text);

        return response()->json(['message' => 'Post updated successfully'], Response::HTTP_OK);
    }

    public function delete(DeletePostRequest $request): JsonResponse
    {
        $postId = $request->route('post_id');

        $this->postService->deletePost($postId);

        return response()->json(['message' => 'Post deleted successfully'], Response::HTTP_OK);
    }

    public function get(GetPostRequest $request): JsonResponse
    {
        $postId = (int) $request->route('post_id');

        $post = $this->postService->getPost($postId);

        return response()->json($post, Response::HTTP_OK);
    }
    public function feed(FeedRequest $request): Response
    {
        $userId = AuthenticatedUser::getId();
        $offset = $request->integer('offset', 0);
        $limit = $request->integer('limit', 10);

        $posts = $this->postService->getFriendFeed($userId, $offset, $limit);

        return response()->json($posts);
    }
}
