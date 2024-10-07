<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ProfileEditForm;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final readonly class ProfileController
{
    public function __construct(
        private Factory $viewFactory,
        #[CurrentUser]
        private User $user,
        private Redirector $redirector,
        private Hasher $hahser,
    ) {}

    /**
     * Route (profile.show): GET /profile
     */
    public function showProfile(): View
    {
        return $this->viewFactory->make('profile', [
            'user' => $this->user,
        ]);
    }

    /**
     * Route (profile.show_edit): GET /profile/edit
     */
    public function showEditForm(): View
    {
        return $this->viewFactory->make('profile-edit', [
            'user' => $this->user,
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Route (profile.edit): PUT /profile/edit
     */
    public function edit(ProfileEditForm $request): RedirectResponse
    {
        $this->user->name = $request->validated('name');
        $this->user->email = $request->validated('email');

        $newPassword = $request->validated('password');
        if ($newPassword !== null) {
            $this->user->password = $this->hahser->make($newPassword);
        }

        $this->user->save();

        return $this->redirector->route('profile.show');
    }
}
