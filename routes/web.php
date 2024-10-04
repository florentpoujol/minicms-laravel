<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Session\Middleware\AuthenticateSession;
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
    ->middleware([

    ])
    ->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLoginForm'])
            ->name('login.show');
        Route::POST('/login', [AuthController::class, 'login'])
            ->name('login.login');
    });

// --------------------------------------------------
// profile

Route::prefix('/profile')
    ->middleware([
        AuthenticateSession::class,
    ])
    ->group(function (): void {
        Route::get('/', [ProfileController::class, 'showProfile'])
            ->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'showEditForm'])
            ->name('profile.show_edit');
        Route::put('/edit', [ProfileController::class, 'edit'])
            ->name('profile.edit');
    });
