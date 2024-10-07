<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

// all the routes here inherits from all of the middlewares of the "web" group
/** @see \Illuminate\Foundation\Configuration\Middleware::getMiddlewareGroups */

// --------------------------------------------------

Route::get('/', [BlogController::class, 'blog'])
    ->name('blog');

Route::get('/posts/{slug}', [BlogController::class, 'showPost'])
    ->name('post.show')
    ->where('slug', '[a-z0-9_-]+');

// --------------------------------------------------
// auth

Route::prefix('/auth')
    ->group(function () {
        Route::middleware([
            RedirectIfAuthenticated::class,
        ])
            ->group(function (): void {
                Route::get('/login', [AuthController::class, 'showLoginForm'])
                    ->name('login.show');
                Route::post('/login', [AuthController::class, 'login'])
                    ->name('login.login');
            });

        Route::middleware([
            Authenticate::class,
        ])
            ->group(function (): void {
                Route::get('/logout', [AuthController::class, 'logout'])
                    ->name('logout');
            });
    });

// --------------------------------------------------
// profile

Route::prefix('/profile')
    ->middleware([
        Authenticate::class,
    ])
    ->group(function (): void {
        Route::get('/', [ProfileController::class, 'showProfile'])
            ->name('profile.show');

        Route::get('/edit/{user?}', [ProfileController::class, 'showEditForm'])
            ->where('user', '\d+')
            ->name('profile.show_edit');

        Route::post('/edit/{user?}', [ProfileController::class, 'edit'])
            ->where('user', '\d+')
            ->name('profile.edit');
    });
