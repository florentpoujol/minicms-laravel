<?php

declare(strict_types=1);

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'blog'])
    ->name('blog');

Route::get('/posts/{slug}', [BlogController::class, 'showPost'])
    ->name('post_show')
    ->where('slug', '[a-z0-9_-]+');
