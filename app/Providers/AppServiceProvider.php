<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Observers\AuditLogObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        Relation::enforceMorphMap([
            // It is important to not start at 0 (zero), so that the whole array is not fully numerical
            // as the system expect an associative array (at least a non-fully numerical one).
            // Otherwise, it builds an assoc array by replacing the keys by the table names.
            1 => User::class,
            2 => Post::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(AuditLogObserver::class);
        Post::observe(AuditLogObserver::class);
    }
}
