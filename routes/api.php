<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users/search', [UserController::class, 'search']);
Route::middleware(AuthenticateWithToken::class)->get('/users/{id}', [UserController::class, 'getUserProfile']);

//friends
Route::middleware(AuthenticateWithToken::class)->get('/friends/add', [FriendController::class, 'addFriend']);
Route::middleware(AuthenticateWithToken::class)->get('/friends/delete', [FriendController::class, 'deleteFriend']);
