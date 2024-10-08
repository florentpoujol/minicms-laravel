<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\QueryBuilder\PostQueryBuilder;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final readonly class AdminPostController
{
    public function __construct(
        private Factory $viewFactory,
        #[CurrentUser]
        private User $loggedInUser,
    ) {}

    /**
     * Route (admin.posts.list): GET /admin/posts
     */
    public function list(): View
    {
        $posts = Post::query()
            ->orderBy('created_at', 'desc')

            ->when($this->loggedInUser->hasWriterRole(), function (PostQueryBuilder $qb): void {
                $qb->whereBelongsTo($this->loggedInUser, 'author');
            })

            ->get();

        return $this->viewFactory->make('admin/posts/list', [
            'posts' => $posts,
        ]);
    }
}
