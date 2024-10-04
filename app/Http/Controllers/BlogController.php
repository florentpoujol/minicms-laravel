<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * Route: GET /{slug}
     */
    public function showPost(
        string $slug,
        #[CurrentUser]
        ?User $user,
    ): View {
        $post = Post::query()
            ->where('slug', '=', $slug)
            ->with('author:id,name')
            ->firstOrFail();

        if (
            $post->published_at === null
            && (
                ($user === null || $user->hasRegularRole()) // anonymous or regular
                || ($user->hasWriterRole() && $user->isNot($post->author)) // writer but not of that post
            )
            // else the writer of the article or an admin that can see any unpublished posts
        ) {
            throw new NotFoundHttpException;
        }

        return $this->viewFactory->make('post', [
            'title' => $post->title,
            'post' => $post,
        ]);
    }
}
