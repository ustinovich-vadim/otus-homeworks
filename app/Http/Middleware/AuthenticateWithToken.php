<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Token\TokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $user = User::find($this->tokenService->getUserIdByToken($token));
        Auth::login($user);
        AuthenticatedUser::setId($user->id);

        return $next($request);
    }
}
