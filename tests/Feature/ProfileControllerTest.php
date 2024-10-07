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
        $response->assertOk();

        $content = $response->getContent();
        self::assertStringContainsString($user->name, $content);
        self::assertStringContainsString($user->role->value, $content);
    }

    public function test_can_see_profile_edit_form_when_logged_in(): void
    {
        // arrange
        $user = User::query()->firstOrFail();

        // act
        $response = $this
            ->actingAs($user)
            ->get('/profile/edit');

        // assert
        $response->assertOk();

        $content = $response->getContent();
        self::assertStringContainsString('<input type="email"', $content);
        self::assertStringContainsString($user->name, $content);
        self::assertStringContainsString($user->role->value, $content);
    }

    public function test_can_edit_profile(): void
    {
        // arrange
        $newName = 'the new name';
        $newEmail = 'the-new-email@example.com';

        $user = User::query()->firstOrFail();
        $oldEmail = $user->email;
        self::assertNotSame($newName, $user->name);
        self::assertNotSame($newEmail, $user->email);

        $this->assertDatabaseMissing('users', ['email' => $newEmail]);

        // act
        $response = $this
            ->actingAs($user)
            ->post('/profile/edit', [
                'name' => $newName,
                'email' => $newEmail,
            ]);

        // assert
        $response->assertRedirectToRoute('profile.show');

        $this->assertDatabaseMissing('users', ['email' => $oldEmail]);
        $this->assertDatabaseHas('users', ['email' => $newEmail]);

        $user->refresh();
        self::assertSame($newName, $user->name);
        self::assertSame($newEmail, $user->email);
    }
}
