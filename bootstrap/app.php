<?php

use App\Http\Middleware\AuthenticateWithToken;
use App\Http\Middleware\LogApiRequestResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => [AuthenticateWithToken::class]],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(LogApiRequestResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
