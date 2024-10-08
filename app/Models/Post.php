<?php

declare(strict_types=1);

namespace App\Models;

use App\QueryBuilder\PostQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property null|Carbon $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $author
 *
 * @method static PostQueryBuilder<self> query()
 */
final class Post extends Model
{
    use HasAuditLogTrait;

    protected static string $builder = PostQueryBuilder::class;

    protected $with = [
        'author:id,name,role',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'title',
        // 'slug',
        'content',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
