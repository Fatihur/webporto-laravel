<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('Two-Factor Authentication') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add an extra layer of security to your account') }}
        </p>
    </div>

    <!-- Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-300 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Status Card -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        @if (auth()->user()->two_factor_enabled && !$isEnabling && !$isDisabling)
            <!-- 2FA Enabled -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Two-factor authentication is enabled') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Your account is protected with two-factor authentication.') }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button
                    wire:click="startDisabling"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    {{ __('Disable 2FA') }}
                </button>
                <button
                    wire:click="regenerateRecoveryCodes"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                >
                    {{ __('Regenerate Recovery Codes') }}
                </button>
            </div>
        @elseif ($isEnabling)
            <!-- Enable 2FA Flow -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Set up Two-Factor Authentication') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold text-sm mr-3">1</span>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('Scan the QR code') }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Use an authenticator app like Google Authenticator or Authy') }}</p>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="bg-white p-4 rounded-lg inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="2FA QR Code" class="mx-auto">
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Or enter this code manually:') }}</p>
                            <code class="text-lg font-mono bg-gray-200 dark:bg-gray-800 px-3 py-1 rounded">{{ $secret }}</code>
                        </div>

                        <div class="flex items-start">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold text-sm mr-3">2</span>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('Enter the verification code') }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Enter the 6-digit code from your authenticator app') }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Verification Code') }}
                            </label>
                            <input
                                type="text"
                                wire:model="code"
                                maxlength="6"
                                class="w-40 text-center text-2xl tracking-widest border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                placeholder="000000"
                            >
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button
                        wire:click="confirmEnable"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        {{ __('Confirm & Enable') }}
                    </button>
                    <button
                        wire:click="cancelEnabling"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
                    >
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>

        @elseif ($isDisabling)
            <!-- Disable 2FA Flow -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Disable Two-Factor Authentication') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Are you sure you want to disable two-factor authentication? This will make your account less secure.') }}
                    </p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Enter your verification code to confirm') }}
                        </label>
                        <input
                            type="text"
                            wire:model="code"
                            maxlength="6"
                            class="w-40 text-center text-2xl tracking-widest border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            placeholder="000000"
                        >
                        @error('code')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4">
                    <button
                        wire:click="confirmDisable"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        {{ __('Disable 2FA') }}
                    </button>
                    <button
                        wire:click="cancelDisabling"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
                    >
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>

        @else
            <!-- 2FA Not Enabled -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Two-factor authentication is not enabled') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Protect your account by enabling two-factor authentication.') }}
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button
                    wire:click="startEnabling"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ __('Enable 2FA') }}
                </button>
            </div>
        @endif
    </div>

    <!-- Recovery Codes Modal -->
    @if ($showRecoveryCodes)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Recovery Codes') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Store these recovery codes in a safe place. You can use them to access your account if you lose your authenticator device.') }}
                </p>
                
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg mb-4">
                    <ul class="space-y-2">
                        @foreach ($recoveryCodes as $code)
                            <li class="font-mono text-sm">{{ $code }}</li>
                        @endforeach
                    </ul>
                </div>

                <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                    {{ __('Each code can only be used once. Keep them secret!') }}
                </p>

                <button
                    wire:click="confirmRecoveryCodes"
                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ __('I have saved my recovery codes') }}
                </button>
            </div>
        </div>
    @endif
</div>
