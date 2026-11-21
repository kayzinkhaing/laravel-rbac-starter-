<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

final class ReCaptcha
{
    public static function enabled(): bool
    {
        return filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    public static function verify(?string $token, string $action): bool
    {
        if (! self::enabled()) {
            return true;
        }

        if (! $token) {
            return false;
        }

        $response = Http::asForm()
            ->acceptJson()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
            ]);

        if (! $response->successful()) {
            return false;
        }

        $payload = $response->json();

        return ($payload['success'] ?? false)
            && ($payload['score'] ?? 0) >= 0.5
            && (empty($payload['action']) || $payload['action'] === $action);
    }
}
