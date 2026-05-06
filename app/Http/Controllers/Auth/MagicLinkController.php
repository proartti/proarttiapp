<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Services\Auth\MagicLinkSender;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Inertia\Response;

class MagicLinkController extends Controller
{
    public function __construct(private readonly MagicLinkSender $magicLinkSender) {}

    /**
     * Show the email request form (replaces the password login form).
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Send a magic link to the given email address.
     * Always responds with the same message to prevent email enumeration.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $throttleKey = 'magic-link:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->with('status', 'Too many requests. Please wait a moment and try again.');
        }

        RateLimiter::hit($throttleKey, 60);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $this->magicLinkSender->send($user);
        }

        return back()->with('status', 'If that email is registered, a sign-in link is on its way.');
    }

    /**
     * Verify a magic link token and log the user in.
     */
    public function verify(Request $request, string $token): RedirectResponse
    {
        $hashed = hash('sha256', $token);

        $loginToken = LoginToken::with('user')
            ->where('token', $hashed)
            ->first();

        if (! $loginToken || ! $loginToken->isValid()) {
            return redirect()->route('login')
                ->with('status', 'This sign-in link is invalid or has expired. Please request a new one.');
        }

        $loginToken->markUsed();

        Auth::login($loginToken->user, remember: true);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
