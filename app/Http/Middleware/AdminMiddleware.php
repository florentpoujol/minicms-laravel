<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Response;

final readonly class AdminMiddleware
{
    public function __construct(
        private UrlGenerator $urlGenerator,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        assert($user === null || $user instanceof User); // @phpstan-ignore-line (phpstan thinks this is always true because the method returns mixed...)

        if ($user === null || $user->hasRegularRole()) {
            throw new AuthenticationException(
                'Must be writer or admin role.',
                [],
                $this->urlGenerator->route('profile.show'),
            );
        }

        return $next($request);
    }
}
