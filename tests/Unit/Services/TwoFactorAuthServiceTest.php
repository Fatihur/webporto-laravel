<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_enabled_returns_true_when_user_has_confirmed_two_factor(): void
    {
        $service = app(TwoFactorAuthService::class);
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($service->generateSecretKey()),
            'two_factor_confirmed_at' => now(),
        ]);

        $this->assertTrue($service->isEnabled($user));
    }

    public function test_verify_recovery_code_consumes_code_once(): void
    {
        $service = app(TwoFactorAuthService::class);
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($service->generateSecretKey()),
        ]);

        $codes = $service->generateRecoveryCodes($user);
        $firstCode = $codes[0];

        $this->assertTrue($service->verifyRecoveryCode($user->fresh(), $firstCode));
        $this->assertFalse($service->verifyRecoveryCode($user->fresh(), $firstCode));
    }
}
