<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginForm;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final readonly class AuthController
{
    public function __construct(
        private Factory $viewFactory,
        /**
         * @var StatefulGuard $guard
         */
        private Guard $guard,
        private UrlGenerator $urlGenerator,
    ) {}

    /**
     * Route: GET /auth/login
     */
    public function showLoginForm(): View
    {
        return $this->viewFactory->make('auth/login');
    }

    /**
     * Route: POST /auth/login
     */
    public function login(LoginForm $request): RedirectResponse
    {
        $validated = $this->guard->once($request->only('email', 'password'));
        if ($validated) {
            $this->guard->login($this->guard->user(), $request->get('remember') === 'on');

            $user = $this->guard->user();
            assert($user instanceof User);

            $routeName = 'profile.show';
            if (! $user->hasRegularRole()) { // writer or admin
                $routeName = 'admin.posts.list';
            }

            return new RedirectResponse($this->urlGenerator->route($routeName));
        }

        return new RedirectResponse($this->urlGenerator->route('login.show'));
    }

    public function logout(): RedirectResponse
    {
        assert($this->guard instanceof StatefulGuard); // for some reason, we can't put StatefulGuard as the type declaration

        $this->guard->logout();

        return new RedirectResponse($this->urlGenerator->route('blog'));
    }
}
