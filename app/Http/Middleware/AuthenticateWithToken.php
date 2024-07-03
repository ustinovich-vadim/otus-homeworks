<?php

namespace App\Http\Middleware;

use App\Services\Token\TokenService;
use Closure;
use Illuminate\Http\Request;

readonly class AuthenticateWithToken
{
    public function __construct(private TokenService $tokenService)
    {
        //
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token || !$this->tokenService->isTokenValid($token)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
