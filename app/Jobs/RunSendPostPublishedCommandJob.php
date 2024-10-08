<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

final class RunSendPostPublishedCommandJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->queue = 'post_published_notifs';
    }

    public function handle(): void
    {
        Artisan::call('app:send-article-published-notifications');
    }
}
