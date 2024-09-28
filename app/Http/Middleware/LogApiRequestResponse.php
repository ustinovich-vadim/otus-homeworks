<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogApiRequestResponse
{
    public function handle(Request $request, Closure $next)
    {
        $requestId = $request->header('x-request-id', Str::uuid());
        $request->headers->set('x-request-id', $requestId);

        Log::info('Incoming Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        $response = $next($request);
        $response->headers->set('x-request-id', $requestId);

        Log::info('Outgoing Response', [
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent(),
        ]);

        return $response;
    }
}
