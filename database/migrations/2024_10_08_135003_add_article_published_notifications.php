<?php

declare(strict_types=1);

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_published_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Post::class)->constrained();
            $table->timestamp('post_will_publish_at');
            $table->timestamp('notif_sent_at')->nullable();
            $table->timestamps();

            $table->unique('post_id', '');
            $table->index(['notif_sent_at', 'post_will_publish_at'], 'post_publish_notif_notif_sent_at_post_will_publish_at_index');
        });
    }

    public function down(): void
    {
        Schema::drop('post_published_notifications');
    }
};
