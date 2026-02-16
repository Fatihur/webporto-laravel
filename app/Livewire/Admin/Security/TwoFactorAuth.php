<?php

namespace App\Livewire\Admin\Security;

use App\Services\TwoFactorAuthService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.admin')]
class TwoFactorAuth extends Component
{
    #[Validate('required|numeric|digits:6')]
    public string $code = '';

    #[Validate('required|string|size:10')]
    public string $recoveryCode = '';

    public bool $showRecoveryCodeInput = false;
    public ?string $qrCodeUrl = null;
    public ?string $secret = null;
    public array $recoveryCodes = [];
    public bool $showRecoveryCodes = false;
    public bool $isEnabling = false;
    public bool $isDisabling = false;

    protected TwoFactorAuthService $twoFactorService;

    public function boot(TwoFactorAuthService $twoFactorService): void
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function mount(): void
    {
        if (!auth()->user()->two_factor_enabled) {
            $this->secret = $this->twoFactorService->generateSecretKey();
            $this->qrCodeUrl = $this->twoFactorService->getQRCodeUrl(
                auth()->user(),
                $this->secret
            );
        }
    }

    public function startEnabling(): void
    {
        $this->isEnabling = true;
        $this->secret = $this->twoFactorService->generateSecretKey();
        $this->qrCodeUrl = $this->twoFactorService->getQRCodeUrl(
            auth()->user(),
            $this->secret
        );
    }

    public function confirmEnable(): void
    {
        $this->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        // Temporarily store the secret for verification
        $user = auth()->user();
        $user->two_factor_secret = encrypt($this->secret);

        if ($this->twoFactorService->enable($user, $this->code)) {
            // Generate and show recovery codes
            $this->recoveryCodes = $this->twoFactorService->generateRecoveryCodes($user);
            $this->showRecoveryCodes = true;
            $this->isEnabling = false;

            session()->flash('success', 'Two-factor authentication has been enabled.');
        } else {
            $this->addError('code', 'Invalid verification code.');
        }

        $this->code = '';
    }

    public function confirmRecoveryCodes(): void
    {
        $this->showRecoveryCodes = false;
        $this->redirect(route('admin.security.2fa'));
    }

    public function startDisabling(): void
    {
        $this->isDisabling = true;
    }

    public function confirmDisable(): void
    {
        $this->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        if ($this->twoFactorService->verifyCode(
            decrypt(auth()->user()->two_factor_secret),
            $this->code
        )) {
            $this->twoFactorService->disable(auth()->user());
            $this->isDisabling = false;

            session()->flash('success', 'Two-factor authentication has been disabled.');
        } else {
            $this->addError('code', 'Invalid verification code.');
        }

        $this->code = '';
    }

    public function cancelEnabling(): void
    {
        $this->isEnabling = false;
        $this->secret = null;
        $this->qrCodeUrl = null;
        $this->code = '';
    }

    public function cancelDisabling(): void
    {
        $this->isDisabling = false;
        $this->code = '';
    }

    public function showRecoveryCodeForm(): void
    {
        $this->showRecoveryCodeInput = true;
    }

    public function verifyRecoveryCode(): void
    {
        $this->validate([
            'recoveryCode' => 'required|string|size:10',
        ]);

        if ($this->twoFactorService->verifyRecoveryCode(auth()->user(), strtoupper($this->recoveryCode))) {
            session()->flash('success', 'Recovery code accepted. Please set up 2FA again.');
            $this->twoFactorService->disable(auth()->user());
            $this->startEnabling();
        } else {
            $this->addError('recoveryCode', 'Invalid recovery code.');
        }

        $this->recoveryCode = '';
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        if ($this->twoFactorService->verifyCode(
            decrypt(auth()->user()->two_factor_secret),
            $this->code
        )) {
            $this->recoveryCodes = $this->twoFactorService->generateRecoveryCodes(auth()->user());
            $this->showRecoveryCodes = true;

            session()->flash('success', 'New recovery codes have been generated.');
        } else {
            $this->addError('code', 'Invalid verification code.');
        }

        $this->code = '';
    }

    public function render()
    {
        return view('livewire.admin.security.two-factor-auth');
    }
}
