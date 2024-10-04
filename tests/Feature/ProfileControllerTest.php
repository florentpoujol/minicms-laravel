<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase;

final class ProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_not_see_profile_when_not_logged_in(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirectToRoute('login.show');
    }

    public function test_can_see_profile_when_logged_in(): void
    {
        // arrange
        $user = User::query()->firstOrFail();

        // act
        $response = $this
            ->actingAs($user)
            ->get('/profile');

        // assert
        $content = $response->getContent();
        self::assertStringContainsString($user->name, $content);
        self::assertStringContainsString($user->role->value, $content);
    }
}
