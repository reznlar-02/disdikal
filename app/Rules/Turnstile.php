<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    public function __construct(private ?string $remoteIp = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => $this->remoteIp,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Turnstile verification request failed: ' . $e->getMessage());
            $fail('Verifikasi keamanan gagal dihubungi, silakan coba lagi.');
            return;
        }

        if (! $response->successful() || ! ($response->json('success') ?? false)) {
            $fail('Verifikasi keamanan gagal, silakan coba lagi.');
        }
    }
}
