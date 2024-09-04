<?php

use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return $user->id === (int) $id;
}, ['middleware' => [AuthenticateWithToken::class]]);

Broadcast::channel('public-channel', function () {
    return true;
});
