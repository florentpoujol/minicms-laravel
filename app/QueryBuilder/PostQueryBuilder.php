<?php

declare(strict_types=1);

namespace App\QueryBuilder;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template-extends Builder<Post>
 */
final class PostQueryBuilder extends Builder
{
    public function whereSlug(string $slug): self
    {
        $this->where('slug', '=', $slug);

        return $this;
    }

    public function wherePublished(): self
    {
        $this->whereNotNull('published_at');

        return $this;
    }

    public function orderByMostRecentlyPublished(): self
    {
        $this->orderBy('published_at', 'desc');

        return $this;
    }
}
