<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /**
     * Generate a new 2FA secret key
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Get QR code URL for the authenticator app
     */
    public function getQRCodeUrl(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
    }

    /**
     * Verify the 2FA code
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return (bool) $this->google2fa->verifyKey($secret, $code, 2);
    }

    /**
     * Enable 2FA for user
     */
    public function enable(User $user, string $code): bool
    {
        $plainSecret = $user->two_factor_secret;

        if (! $plainSecret) {
            return false;
        }

        if (! $this->verifyCode($plainSecret, $code)) {
            return false;
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($plainSecret),
            'two_factor_confirmed_at' => now(),
        ]);

        // Generate recovery codes
        $this->generateRecoveryCodes($user);

        return true;
    }

    /**
     * Disable 2FA for user
     */
    public function disable(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);
    }

    /**
     * Generate recovery codes
     */
    public function generateRecoveryCodes(User $user): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = $this->generateRecoveryCode();
        }

        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ]);

        return $codes;
    }

    /**
     * Generate a single recovery code
     */
    protected function generateRecoveryCode(): string
    {
        return strtoupper(substr(md5(random_bytes(32)), 0, 10));
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        if (! $user->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (! is_array($codes)) {
            return false;
        }

        if (($key = array_search($code, $codes, true)) !== false) {
            unset($codes[$key]);
            $user->update([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($codes))),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Check if user has 2FA enabled
     */
    public function isEnabled(User $user): bool
    {
        return $user->two_factor_enabled && $user->two_factor_confirmed_at !== null;
    }

    /**
     * Store temporary 2FA verification in session
     */
    public function markAsVerified(): void
    {
        session()->put('two_factor_verified', true);
        session()->put('two_factor_verified_at', now()->timestamp);
    }

    /**
     * Check if current session has 2FA verification
     */
    public function isVerified(): bool
    {
        if (! session()->has('two_factor_verified')) {
            return false;
        }

        // Verification expires after 6 hours
        $verifiedAt = session()->get('two_factor_verified_at', 0);

        return (now()->timestamp - $verifiedAt) < 21600;
    }

    /**
     * Clear 2FA verification from session
     */
    public function clearVerification(): void
    {
        session()->forget(['two_factor_verified', 'two_factor_verified_at']);
    }

    /**
     * Rate limit 2FA attempts
     */
    public function hasTooManyAttempts(User $user): bool
    {
        $key = "2fa_attempts:{$user->id}";
        $attempts = Cache::get($key, 0);

        return $attempts >= 5;
    }

    /**
     * Increment 2FA attempt counter
     */
    public function incrementAttempts(User $user): void
    {
        $key = "2fa_attempts:{$user->id}";
        Cache::put($key, Cache::get($key, 0) + 1, now()->addMinutes(15));
    }

    /**
     * Clear 2FA attempt counter
     */
    public function clearAttempts(User $user): void
    {
        Cache::forget("2fa_attempts:{$user->id}");
    }
}
