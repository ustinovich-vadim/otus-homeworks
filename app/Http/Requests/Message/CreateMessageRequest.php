<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'required|string',
            'user_id' => 'required|int|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'The user you are trying to send a message to does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->route('user_id'),
        ]);
    }
}
