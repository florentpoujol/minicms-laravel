<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final readonly class ProfileController
{
    public function __construct(
        private Factory $viewFactory,
        #[CurrentUser]
        private User $user,
    ) {}

    public function showProfile(): View
    {
        return $this->viewFactory->make('profile', [
            'user' => $this->user,
        ]);
    }
}
