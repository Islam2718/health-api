<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_user_is_created_in_the_users_table_and_receives_a_token(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-user-123');
        $googleUser->shouldReceive('getEmail')->andReturn('google@example.com');
        $googleUser->shouldReceive('getName')->andReturn('Google User');

        Socialite::shouldReceive('driver')->once()->with('google')->andReturnSelf();
        Socialite::shouldReceive('userFromToken')->once()->with('google-access-token')->andReturn($googleUser);

        $response = $this->postJson('/api/login', [
            'access_token' => 'google-access-token',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Google login successful');
        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('users', [
            'google_id' => 'google-user-123',
            'email' => 'google@example.com',
        ]);

        $user = User::where('google_id', 'google-user-123')->firstOrFail();
        $this->assertNotEmpty($user->password);
    }
}
