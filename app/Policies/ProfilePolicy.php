<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final readonly class ProfilePolicy
{
    public function update(User $loggedInUser, User $model): bool
    {
        return $loggedInUser->id === $model->id
            || ($loggedInUser->hasAdminRole() && ! $model->hasAdminRole());
    }
}
