<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_we_can_see_the_login_form_when_not_logged_in(): void
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200);

        $responseContent = $response->getContent();
        self::assertStringContainsString('Email:', $responseContent);
    }

    /**
     * @return iterable<string, array<string, string|bool>>
     */
    public static function getLoggingTestCases(): iterable
    {
        yield 'validation error' => [
            'email' => 'not an email',
            'password' => '', // less than 8 chars
            'expectedRedirectRoute' => '', // when validation errors, the route isn't actually redirected ?
            'expectValidationErrors' => true,
        ];

        yield 'good email and wrong password' => [
            'email' => 'writer@example.com',
            'password' => 'not the write password',
            'expectedRedirectRoute' => 'login.show',
            'expectValidationErrors' => false,
        ];

        yield 'wrong email and good password' => [
            'email' => 'whatever@example.com',
            'password' => 'writer',
            'expectedRedirectRoute' => 'login.show',
            'expectValidationErrors' => false,
        ];

        yield 'regular should login' => [
            'email' => 'regular@example.com',
            'password' => 'regular',
            'expectedRedirectRoute' => 'profile.show',
            'expectValidationErrors' => false,
        ];

        yield 'writer should login' => [
            'email' => 'writer@example.com',
            'password' => 'writer',
            'expectedRedirectRoute' => 'admin.posts.list',
            'expectValidationErrors' => false,
        ];
    }

    #[DataProvider('getLoggingTestCases')]
    public function test_login(string $email, string $password, string $expectedRedirectRoute, bool $expectValidationErrors): void
    {
        $response = $this
            ->post('/auth/login', compact('email', 'password'));

        if ($expectValidationErrors) {
            $response->assertSessionHasErrors();
        }

        if ($expectedRedirectRoute !== '') {
            $response->assertRedirectToRoute($expectedRedirectRoute);
        }
    }

    public function test_logout_isnt_accessible_when_not_loggedin(): void
    {
        $response = $this->get('/auth/logout');

        $response->assertRedirectToRoute('login.show');
    }

    public function test_logout_when_loggedin(): void
    {
        $user = User::query()->firstOrFail();

        $response = $this
            ->actingAs($user)
            ->get('/auth/logout');

        $response->assertRedirectToRoute('blog');
    }
}
