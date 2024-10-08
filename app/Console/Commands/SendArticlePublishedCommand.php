<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PostPublishedNotificationModel;
use App\Notifications\PostPublishedNotification;
use Illuminate\Console\Command;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Carbon;

final class SendArticlePublishedCommand extends Command
{
    public function __construct(
        private Dispatcher $notifDispatcher,
    ) {
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $signature = 'app:send-article-published-notifications';

    /**
     * @var string
     */
    protected $description = 'Send the PostPublishedNotification to writers when their posts planned to be published later finally were published. Runs via the scheduler every 5 minutes.';

    public function handle(): int
    {
        $notifsToSend = PostPublishedNotificationModel::query()
            ->whereNull('notif_sent_at')
            ->whereBetween('post_will_publish_at', [
                new Carbon('- 30 min'),
                new Carbon,
            ])
            ->orderBy('post_will_published_at', 'asc') // oldest first
            ->with([
                'post:id,title,slug,author_id',
                'post.author:id,email',
            ])
            ->get();

        /** @var PostPublishedNotificationModel $notifToSend */
        foreach ($notifsToSend as $notifToSend) {
            $notification = new PostPublishedNotification(
                $notifToSend->post->title,
                $notifToSend->post->slug,
            );

            $this->notifDispatcher->sendNow($notifToSend->post->author, $notification);
            // ideally we should queue them too, but I'm not sure how to update the relevant PostPublishedNotificationModel
            // once it is actually sent

            $notifToSend->notif_sent_at = Carbon::now();
            $notifToSend->save();
        }

        return 0;
    }
}
