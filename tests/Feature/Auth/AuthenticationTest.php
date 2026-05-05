<?php

namespace Tests\Feature\Auth;

use App\Models\LoginToken;
use App\Models\User;
use App\Mail\MagicLinkMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_magic_link_is_sent_for_existing_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(MagicLinkMail::class, fn ($mail) => $mail->hasTo($user->email));

        $this->assertDatabaseHas('login_tokens', ['user_id' => $user->id]);
    }

    public function test_no_email_is_sent_for_unknown_address(): void
    {
        Mail::fake();

        $this->post('/login', ['email' => 'nobody@example.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        // Response must be identical to prevent email enumeration
        Mail::assertNothingSent();
    }

    public function test_valid_token_logs_user_in(): void
    {
        $user = User::factory()->create();
        $plain = 'a' . str_repeat('b', 63);

        LoginToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $plain),
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->get(route('magic-link.verify', ['token' => $plain]))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_token_cannot_be_reused(): void
    {
        $user = User::factory()->create();
        $plain = 'a' . str_repeat('b', 63);

        $token = LoginToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $plain),
            'expires_at' => now()->addMinutes(15),
        ]);

        // First use
        $this->get(route('magic-link.verify', ['token' => $plain]));
        $this->assertAuthenticatedAs($user);

        // Second use — must fail
        auth()->logout();
        $this->get(route('magic-link.verify', ['token' => $plain]))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_expired_token_is_rejected(): void
    {
        $user = User::factory()->create();
        $plain = 'a' . str_repeat('b', 63);

        LoginToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $plain),
            'expires_at' => now()->subMinute(),
        ]);

        $this->get(route('magic-link.verify', ['token' => $plain]))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_invalid_token_is_rejected(): void
    {
        $this->get(route('magic-link.verify', ['token' => 'not-a-real-token']))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
