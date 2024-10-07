<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
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
            'email' => ['required', 'max:255', 'email'], // no unique rule here, see method ensureEmailIsUnique() below

            'password' => ['nullable', 'string', 'min:5'], // our test passwords are small (admin, writer, regular)
            'password_confirm' => ['nullable', 'required_with:password', 'same:password', 'string', 'min:5'], // our test passwords are small (admin, writer, regular)
        ];
    }

    public function ensureNewEmailIsUnique(User $editedUser): void
    {
        // We can't use the unique rule anymore since we do not know which email to ignore when the edited user isn't the logged-in one
        $this->validate([
            Rule::unique('users', 'email')->ignoreModel($editedUser),
        ]);
    }
}
