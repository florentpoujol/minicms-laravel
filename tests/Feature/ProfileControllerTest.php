<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
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

    public function test_a_non_admin_can_not_edit_another_user(): void
    {
        // arrange
        $regular = User::query()
            ->where('role', '=', UserRole::REGULAR)
            ->firstOrFail();

        $writer = User::query()
            ->where('role', '=', UserRole::WRITER)
            ->firstOrFail();

        // act
        $response = $this
            ->actingAs($writer)
            ->get('/profile/edit/'.$regular->id);

        // assert
        $response->assertForbidden(); // 403
    }

    public function test_an_admin_can_see_the_edit_form_for_another_user(): void
    {
        // arrange
        $admin = User::query()
            ->where('role', '=', UserRole::ADMIN)
            ->firstOrFail();

        $writer = User::query()
            ->where('role', '=', UserRole::WRITER)
            ->firstOrFail();

        // act
        $response = $this
            ->actingAs($admin)
            ->get('/profile/edit/'.$writer->id);

        // assert
        $response->assertOk();

        $content = $response->getContent();
        self::assertStringContainsString('<input type="email"', $content);
        self::assertStringContainsString($writer->name, $content);
        self::assertStringContainsString($writer->role->value, $content);
    }

    public function test_an_admin_can_edit_another_user(): void
    {
        // arrange
        $newName = 'the new name';
        $newEmail = 'the-new-email@example.com';

        $admin = User::query()
            ->where('role', '=', UserRole::ADMIN)
            ->firstOrFail();

        $writer = User::query()
            ->where('role', '=', UserRole::WRITER)
            ->firstOrFail();

        $oldEmail = $writer->email;
        self::assertNotSame($newName, $writer->name);
        self::assertNotSame($newEmail, $writer->email);

        $this->assertDatabaseMissing('users', ['email' => $newEmail]);

        // act
        $response = $this
            ->actingAs($admin)
            ->post('/profile/edit/'.$writer->id, [
                'name' => $newName,
                'email' => $newEmail,
            ]);

        // assert
        $response->assertRedirectToRoute('profile.show_edit', ['user' => $writer->id]);

        $this->assertDatabaseMissing('users', ['email' => $oldEmail]);
        $this->assertDatabaseHas('users', ['email' => $newEmail]);

        $writer->refresh();
        self::assertSame($newName, $writer->name);
        self::assertSame($newEmail, $writer->email);
    }
}
