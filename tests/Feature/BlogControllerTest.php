<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

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

    // --------------------------------------------------

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

    public function test_writers_can_only_see_their_unpublished_posts(): void
    {
        // arrange
        $otherWriter = UserFactory::new()->create([
            'role' => UserRole::WRITER,
        ]);

        self::assertEmpty($otherWriter->posts);

        $post = Post::query()
            ->whereNull('published_at')
            ->firstOrFail();

        // act
        $response = $this
            ->actingAs($otherWriter)
            ->get('/posts/'.$post->slug);

        // assert
        $response->assertStatus(404);
    }

    public function test_admins_can_see_all_unpublished_posts(): void
    {
        // arrange
        $admin = User::query()
            ->where('role', '=', UserRole::ADMIN)
            ->firstOrFail();

        $post = Post::query()
            ->whereNull('published_at')
            ->whereNot('author_id', '=', $admin->id)
            ->firstOrFail();

        // act
        $response = $this
            ->actingAs($admin)
            ->get('/posts/'.$post->slug);

        // assert
        $response->assertStatus(200);

        $responseContent = $response->getContent();
        self::assertStringContainsString($post->title, $responseContent);
        self::assertStringContainsString($post->author->name, $responseContent);
    }

    // --------------------------------------------------
    // Same tests, but using a single test method and a data provider.

    // Which is actually really ridiculous since data providers must be
    // static and Laravel isn't booted inside them,
    // and everything that requires it, like SQL requests,
    // must be wrapped in a closure to be manually called in the test...

    /**
     * @return iterable<string, array<string, null|int|\Closure>>
     */
    public static function getTestCases(): iterable
    {
        yield 'test_regular_users_can_see_published_posts' => [
            'loggedInUserGetter' => null,
            'postGetter' => fn () => Post::query()
                ->whereNotNull('published_at')
                ->firstOrFail(),
            'expectedStatus' => 200,
        ];

        yield 'test_regular_users_can_not_see_unpublished_posts' => [
            'loggedInUserGetter' => null,
            'postGetter' => fn () => Post::query()
                ->whereNull('published_at')
                ->firstOrFail(),
            'expectedStatus' => 404,
        ];

        yield 'test_post_authors_can_see_their_post_even_when_not_published' => [
            'loggedInUserGetter' => fn () => Post::query()
                ->whereNull('published_at')
                ->firstOrFail()->author,
            'postGetter' => fn () => Post::query()
                ->whereNull('published_at')
                ->firstOrFail(),
            'expectedStatus' => 200,
        ];

        yield 'test_writers_can_only_see_their_unpublished_posts' => [
            'loggedInUserGetter' => fn () => UserFactory::new()->create([
                'role' => UserRole::WRITER,
            ]),
            'postGetter' => fn () => Post::query()
                ->whereNull('published_at')
                ->firstOrFail(),
            'expectedStatus' => 404,
        ];

        yield 'test_admins_can_see_all_unpublished_posts' => [
            'loggedInUserGetter' => fn () => User::query()
                ->where('role', '=', UserRole::ADMIN)
                ->firstOrFail(),
            'postGetter' => fn () => Post::query()
                ->whereNull('published_at')
                ->whereNot('author_id', '=', User::query()
                    ->where('role', '=', UserRole::ADMIN)
                    ->firstOrFail('id')->id)
                ->firstOrFail(),
            'expectedStatus' => 200,
        ];
    }

    /**
     * @param  null|(\Closure(): User)  $loggedInUserGetter
     * @param  (\Closure(): Post)  $postGetter
     */
    #[DataProvider('getTestCases')]
    public function test_can_see_post_or_not(?\Closure $loggedInUserGetter, \Closure $postGetter, int $expectedStatus): void
    {
        // arrange

        if ($loggedInUserGetter !== null) {
            $this->actingAs($loggedInUserGetter());
        }

        $post = $postGetter();

        // act
        $response = $this
            ->get('/posts/'.$post->slug);

        // assert
        $response->assertStatus($expectedStatus);

        if ($expectedStatus === 200) {
            $responseContent = $response->getContent();
            self::assertStringContainsString($post->title, $responseContent);
            self::assertStringContainsString($post->author->name, $responseContent);
        }
    }
}
