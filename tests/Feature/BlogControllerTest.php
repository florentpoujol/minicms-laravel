<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase;

final class BlogControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_blog_displays_published_posts(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $lastPost = Post::query()
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->firstOrFail();

        $responseContent = $response->getContent();
        self::assertStringContainsString($lastPost->title, $responseContent);
    }
}
