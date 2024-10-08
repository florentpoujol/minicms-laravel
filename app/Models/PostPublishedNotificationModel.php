<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * This model match the post_published_notifications table, where one entry is added
 * when a writer mark a post to be published in the future.
 * The SendArticlePublishedCommand command runs via the Scheduler every 5 minutes
 * and checks that some article indeed were published since the last time it ran
 * and notify their author with the PostPublishedNotification.
 *
 * @property int $id
 * @property int $post_id
 * @property Carbon $post_will_publish_at
 * @property null|Carbon $notif_sent_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Post $post
 *
 * @method static Builder<self> query()
 */
final class PostPublishedNotificationModel extends Model
{
    protected $table = 'post_published_notifications';

    protected $casts = [
        'post_will_publish_at' => 'datetime',
        'notif_sent_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Post, self>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
