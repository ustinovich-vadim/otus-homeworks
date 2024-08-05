<?php

namespace App\Http\Requests\Friend;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Auth\AuthenticatedUser;
use Illuminate\Support\Facades\DB;

class AddFriendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'friend_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value === AuthenticatedUser::getId()) {
                        $fail('You cannot add yourself as a friend.');

                        return;
                    }

                    if ($this->isAlreadyFriend(AuthenticatedUser::getId(), $value)) {
                        $fail('This user is already your friend.');
                    }
                },
            ],
        ];
    }

    private function isAlreadyFriend(int $userId, int $friendId): bool
    {
        return DB::table('friends')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->exists();
    }
}
