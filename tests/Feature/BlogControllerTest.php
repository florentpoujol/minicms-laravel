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

    public function test_regular_users_can_see_published_posts(): void
    {
        // arrange
        $post = Post::query()
            ->whereNotNull('published_at')
            ->firstOrFail();

        // act
        $response = $this->get('/posts/'.$post->slug);

        // assert
        $response->assertStatus(200);

        $responseContent = $response->getContent();
        self::assertStringContainsString($post->title, $responseContent);
        self::assertStringContainsString($post->author->name, $responseContent);
    }

    public function test_regular_users_can_not_see_unpublished_posts(): void
    {
        // arrange
        $post = Post::query()
            ->whereNull('published_at')
            ->firstOrFail();

        // act
        $response = $this->get('/posts/'.$post->slug);

        // assert
        $response->assertStatus(404);
    }

    public function test_post_authors_can_see_their_post_even_when_not_published(): void
    {
        // arrange
        $post = Post::query()
            ->whereNull('published_at')
            ->firstOrFail();

        // act
        $response = $this
            ->actingAs($post->author)
            ->get('/posts/'.$post->slug);

        // assert
        $response->assertStatus(200);

        $responseContent = $response->getContent();
        self::assertStringContainsString($post->title, $responseContent);
        self::assertStringContainsString($post->author->name, $responseContent);
    }
}
