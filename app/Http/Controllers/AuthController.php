<?php

namespace App\Http\Controllers;

use App\DTO\UserRegisterDTO;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Services\Token\TokenService;
use App\Services\User\UserService;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $userService, private readonly TokenService $tokenService)
    {
        //
    }
    public function register(UserRegisterRequest $request): Response
    {
        $this->userService->register(
            new UserRegisterDTO(
                name: $request->string('name'),
                surname: $request->string('surname'),
                birth_date: $request->date('birth_date'),
                gender: $request->string('gender'),
                hobbies: $request->string('hobbies'),
                city: $request->string('city'),
                email: $request->string('email'),
                password: $request->string('password')
        ));

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * @throws ValidationException
     */
    public function login(UserLoginRequest $request): Response
    {
        $user = $this->userService->findByEmail($request->string('email'));

        if (is_null($user) || !$this->userService->validatePassword($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->tokenService->createToken($user->id);

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }
}
