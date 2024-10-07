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
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class ProfileController
{
    public function __construct(
        private Factory $viewFactory,
        #[CurrentUser]
        private User $user,
        private Redirector $redirector,
        private Hasher $hasher,
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
     * Route (profile.show_edit): GET /profile/edit[/{user}]
     */
    public function showEditForm(?User $user = null): View
    {
        if ($user !== null && $this->user->cannot('update', $user)) {
            throw new HttpException(403);
        }

        return $this->viewFactory->make('profile-edit', [
            'user' => $user ?: $this->user,
            'adminEditAnotherOne' => $this->user->isNot($user),
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Route (profile.edit): PUT /profile/edit[/{user}]
     */
    public function edit(ProfileEditForm $request, ?User $user = null): RedirectResponse
    {
        if ($user !== null && $this->user->cannot('update', $user)) {
            throw new HttpException(403);
        }

        $user = $user ?: $this->user;

        $user->name = $request->validated('name');
        $newEmail = $request->validated('email');
        if ($newEmail !== $user->email) {
            $request->ensureNewEmailIsUnique($user);
            $user->email = $newEmail;
        }

        $newPassword = $request->validated('password');
        if ($newPassword !== null) {
            $user->password = $this->hasher->make($newPassword);
        }

        $user->save();

        if ($user->isNot($this->user)) {
            return $this->redirector->route('profile.edit', ['user' => $user->id]);
        }

        return $this->redirector->route('profile.show');
    }
}
