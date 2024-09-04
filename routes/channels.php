<?php

use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.151', function () {
    return true;
}, ['middleware' => [AuthenticateWithToken::class]]);

Broadcast::channel('public-channel', function () {
    return true;
});
