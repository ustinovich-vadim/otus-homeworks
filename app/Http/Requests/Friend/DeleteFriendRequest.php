<?php

namespace App\Http\Requests\Friend;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Auth\AuthenticatedUser;
use Illuminate\Support\Facades\DB;

class DeleteFriendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (!$this->isFriend(AuthenticatedUser::getId(), $value)) {
                        $fail('The specified user is not your friend.');
                    }
                },
            ],
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all($keys);
        $data['user_id'] = $this->route('user_id');

        return $data;
    }

    private function isFriend(int $userId, int $friendId): bool
    {
        return DB::table('friends')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->exists();
    }
}
