<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Auth\AuthenticatedUser;
use Illuminate\Support\Facades\DB;

class DeletePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $postId = $this->route('post_id');
        $userId = AuthenticatedUser::getId();

        return DB::table('posts')
            ->where('id', $postId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'post_id' => 'required|exists:posts,id',
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all($keys);
        $data['post_id'] = $this->route('post_id');

        return $data;
    }
}
