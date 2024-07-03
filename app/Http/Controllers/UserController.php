<?php

namespace App\Http\Controllers;

use App\Services\User\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
        //
    }

    public function getUserProfile(Request $request): Response
    {
        $user = $this->userService->findById((int) $request->route('id'));

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'birth_date' => $user->birth_date,
            'gender' => $user->gender,
            'hobbies' => $user->hobbies,
            'city' => $user->city,
            'email' => $user->email,
        ]);
    }
}
