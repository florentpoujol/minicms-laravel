<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LoginForm extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'max:255', 'email'],
            'password' => ['required', 'string'],
            'remember-me' => ['nullable', 'in:on'], // checkbox, so key is missing when not checked, value is"on" when checked
        ];
    }
}
