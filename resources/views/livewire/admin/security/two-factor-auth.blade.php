<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
            Two-Factor Authentication
        </h1>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
            Add an extra layer of security to your account
        </p>
    </div>

    <!-- Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-300 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <!-- Status Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 mb-6">
        @if (auth()->user()->two_factor_enabled && !$isEnabling && !$isDisabling)
            <!-- 2FA Enabled -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                        Two-factor authentication is enabled
                    </h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Your account is protected with two-factor authentication.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button
                    wire:click="startDisabling"
                    class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-bold"
                >
                    Disable 2FA
                </button>
                <button
                    wire:click="regenerateRecoveryCodes"
                    class="px-4 py-2 bg-zinc-600 text-white rounded-xl hover:bg-zinc-700 transition-colors font-bold"
                >
                    Regenerate Recovery Codes
                </button>
            </div>
        @elseif ($isEnabling)
            <!-- Enable 2FA Flow -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        Set up Two-Factor Authentication
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold text-sm flex-shrink-0">1</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Scan the QR code</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Use an authenticator app like Google Authenticator or Authy</p>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="bg-white p-4 rounded-xl inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="2FA QR Code" class="mx-auto">
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">Or enter this code manually:</p>
                            <code class="text-lg font-mono bg-zinc-200 dark:bg-zinc-800 px-3 py-1 rounded-lg">{{ $secret }}</code>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold text-sm flex-shrink-0">2</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Enter the verification code</p>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Enter the 6-digit code from your authenticator app</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Verification Code
                            </label>
                            <input
                                type="text"
                                wire:model="code"
                                maxlength="6"
                                class="w-full sm:w-40 text-center text-2xl tracking-widest border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-3 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100"
                                placeholder="000000"
                            >
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        wire:click="confirmEnable"
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-bold"
                    >
                        Confirm & Enable
                    </button>
                    <button
                        wire:click="cancelEnabling"
                        class="px-6 py-3 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl hover:border-zinc-400 transition-colors font-bold"
                    >
                        Cancel
                    </button>
                </div>
            </div>

        @elseif ($isDisabling)
            <!-- Disable 2FA Flow -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">
                        Disable Two-Factor Authentication
                    </h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                        Are you sure you want to disable two-factor authentication? This will make your account less secure.
                    </p>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Enter your verification code to confirm
                        </label>
                        <input
                            type="text"
                            wire:model="code"
                            maxlength="6"
                            class="w-full sm:w-40 text-center text-2xl tracking-widest border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-3 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100"
                            placeholder="000000"
                        >
                        @error('code')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        wire:click="confirmDisable"
                        class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-bold"
                    >
                        Disable 2FA
                    </button>
                    <button
                        wire:click="cancelDisabling"
                        class="px-6 py-3 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl hover:border-zinc-400 transition-colors font-bold"
                    >
                        Cancel
                    </button>
                </div>
            </div>

        @else
            <!-- 2FA Not Enabled -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                        Two-factor authentication is not enabled
                    </h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Protect your account by enabling two-factor authentication.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button
                    wire:click="startEnabling"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-bold"
                >
                    Enable 2FA
                </button>
            </div>
        @endif
    </div>

    <!-- Recovery Codes Modal -->
    @if ($showRecoveryCodes)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 max-w-md w-full">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-4">
                    Recovery Codes
                </h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                    Store these recovery codes in a safe place. You can use them to access your account if you lose your authenticator device.
                </p>

                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl mb-4">
                    <ul class="space-y-2">
                        @foreach ($recoveryCodes as $code)
                            <li class="font-mono text-sm">{{ $code }}</li>
                        @endforeach
                    </ul>
                </div>

                <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                    Each code can only be used once. Keep them secret!
                </p>

                <button
                    wire:click="confirmRecoveryCodes"
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-bold"
                >
                    I have saved my recovery codes
                </button>
            </div>
        </div>
    @endif
</div>
