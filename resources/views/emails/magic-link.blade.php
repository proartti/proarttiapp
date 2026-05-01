<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f9fafb; margin: 0; padding: 40px 16px; color: #111827; }
        .card { background: #fff; border-radius: 8px; max-width: 480px; margin: 0 auto; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        h1 { font-size: 20px; font-weight: 600; margin: 0 0 8px; }
        p { color: #6b7280; line-height: 1.6; margin: 0 0 24px; font-size: 14px; }
        .btn { display: inline-block; background: #111827; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 500; }
        .url { margin-top: 24px; font-size: 12px; color: #9ca3af; word-break: break-all; }
        .expiry { font-size: 12px; color: #9ca3af; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Sign in to {{ config('app.name') }}</h1>
        <p>Click the button below to sign in. This link expires in 15 minutes and can only be used once.</p>
        <a href="{{ $magicUrl }}" class="btn">Sign in</a>
        <div class="url">
            <p>Or copy this URL into your browser:</p>
            {{ $magicUrl }}
        </div>
        <p class="expiry">If you didn't request this, you can safely ignore this email.</p>
    </div>
</body>
</html>
