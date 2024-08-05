<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class FeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offset' => 'sometimes|integer|min:0',
            'limit'  => 'sometimes|integer|min:1|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'offset.integer' => 'The offset must be an integer.',
            'offset.min'     => 'The offset cannot be negative.',
            'limit.integer'  => 'The limit must be an integer.',
            'limit.min'      => 'The limit must be at least 1.',
            'limit.max'      => 'The limit cannot exceed 1000.',
        ];
    }
}
