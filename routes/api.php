<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Support\Facades\Route;

//auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//users
Route::get('/users/search', [UserController::class, 'search']);
Route::middleware(AuthenticateWithToken::class)->get('/users/{id}', [UserController::class, 'getUserProfile']);

//friends
Route::middleware(AuthenticateWithToken::class)->post('/friends', [FriendController::class, 'addFriend']);
Route::middleware(AuthenticateWithToken::class)->delete('/friends/{user_id}', [FriendController::class, 'deleteFriend']);

//posts
Route::middleware(AuthenticateWithToken::class)->group(function () {
    Route::middleware(AuthenticateWithToken::class)->get('/posts/feed', [PostController::class, 'feed']);
    Route::post('/posts', [PostController::class, 'create']);
    Route::put('/posts/{post_id}', [PostController::class, 'update']);
    Route::delete('/posts/{post_id}', [PostController::class, 'delete']);
    Route::get('/posts/{post_id}', [PostController::class, 'get']);
});
