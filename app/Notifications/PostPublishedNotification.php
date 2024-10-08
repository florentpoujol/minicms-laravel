<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

final class PostPublishedNotification extends Notification
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $postTitle,
        private readonly string $postSlug,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your article \"$this->postTitle\" was just published !")
            ->line("Your article, titled \"$this->postTitle\" was just published a few minutes ago.")
            ->action('Go review it', URL::route('post.show', ['slug' => $this->postSlug]));
    }
}
