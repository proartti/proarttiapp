<?php

namespace App\Services\Auth;

use App\Mail\MagicLinkMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MagicLinkSender
{
    public function send(User $user): void
    {
        LoginToken::where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        $plainToken = Str::random(64);

        $token = LoginToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $plainToken),
            'expires_at' => now()->addMinutes(15),
        ]);

        $token->token = $plainToken;

        Mail::to($user->email)->send(new MagicLinkMail($token));
    }
}