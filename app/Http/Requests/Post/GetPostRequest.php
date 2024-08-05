<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class GetPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
