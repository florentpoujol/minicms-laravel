<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Post;
use App\Models\PostPublishedNotificationModel;
use App\Models\User;
use App\Observers\AuditLogObserver;
use App\Policies\ProfilePolicy;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        Relation::enforceMorphMap([
            // It is important to not start at 0 (zero), so that the whole array is not fully numerical
            // as the system expect an associative array (at least a non-fully numerical one).
            // Otherwise, it builds an assoc array by replacing the keys by the table names.
            1 => User::class,
            2 => Post::class,
            3 => PostPublishedNotificationModel::class,
        ]);
    }

    public function boot(): void
    {
        User::observe(AuditLogObserver::class);
        Post::observe(AuditLogObserver::class);
        PostPublishedNotificationModel::observe(AuditLogObserver::class);

        // --------------------------------------------------

        // This is "needed" because by default the Authenticate middleware will redirect to the "login" route,
        // but we choose to have this route named "login.show".
        Authenticate::redirectUsing(function (): string {
            /** @var UrlGenerator $urlGenerator */
            $urlGenerator = $this->app->make(UrlGenerator::class);

            return $urlGenerator->route('login.show');
        });

        // --------------------------------------------------

        Gate::policy(User::class, ProfilePolicy::class);
    }
}
