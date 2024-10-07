<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProfileEditForm extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', Rule::unique('users', 'email')->ignoreModel($this->user())],

            'password' => ['nullable', 'string', 'min:5'], // our test passwords are small (admin, writer, regular)
            'password_confirm' => ['nullable', 'required_with:password', 'same:password', 'string', 'min:5'], // our test passwords are small (admin, writer, regular)
        ];
    }
}
