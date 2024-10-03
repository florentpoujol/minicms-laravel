<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final readonly class BlogController
{
    public function __construct(
        private Factory $viewFactory
    ) {}

    /**
     * Route: GET /
     */
    public function blog(): View
    {
        $posts = Post::query()
            ->whereNotNull('published_at')
            ->limit(20)
            ->orderBy('published_at', 'desc') // most recent first
            ->with('author:id,name')
            ->get();

        return $this->viewFactory->make('blog', [
            'posts' => $posts,
        ]);
    }
}
