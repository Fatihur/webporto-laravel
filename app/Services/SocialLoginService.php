<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialLoginService
{
    /**
     * Handle social login/registration
     */
    public function handleSocialLogin(string $provider, SocialiteUser $socialiteUser): User
    {
        // Check if social account exists
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialiteUser->getId())
            ->first();

        if ($socialAccount) {
            return $socialAccount->user;
        }

        // Check if user with this email exists
        $user = User::where('email', $socialiteUser->getEmail())->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? 'User',
                'email' => $socialiteUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }

        // Link social account to user
        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialiteUser->getId(),
            'provider_token' => $socialiteUser->token,
            'provider_refresh_token' => $socialiteUser->refreshToken,
            'nickname' => $socialiteUser->getNickname(),
            'avatar' => $socialiteUser->getAvatar(),
        ]);

        return $user;
    }

    /**
     * Get available social providers
     */
    public function getAvailableProviders(): array
    {
        $providers = [];
        
        foreach (['github', 'google', 'twitter', 'linkedin', 'facebook'] as $provider) {
            if (config("services.{$provider}.client_id") && config("services.{$provider}.client_secret")) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    /**
     * Check if provider is enabled
     */
    public function isProviderEnabled(string $provider): bool
    {
        return in_array($provider, $this->getAvailableProviders());
    }

    /**
     * Unlink social account from user
     */
    public function unlinkAccount(User $user, string $provider): bool
    {
        // Don't allow unlinking if user has no password and this is their only login method
        if (!$user->password && $user->socialAccounts()->count() === 1) {
            return false;
        }

        return $user->socialAccounts()->where('provider', $provider)->delete() > 0;
    }

    /**
     * Get social account for user and provider
     */
    public function getSocialAccount(User $user, string $provider): ?SocialAccount
    {
        return $user->socialAccounts()->where('provider', $provider)->first();
    }

    /**
     * Login user and regenerate session
     */
    public function login(User $user): void
    {
        Auth::login($user, true);
        session()->regenerate();
    }
}
