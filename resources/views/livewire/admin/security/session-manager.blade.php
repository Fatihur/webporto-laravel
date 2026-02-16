<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Active Sessions
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Manage your active sessions across all devices
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

    <!-- Sessions List -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        @if (count($sessions) > 1)
            <div class="p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <button
                    wire:click="terminateAllOtherSessions"
                    wire:confirm="Are you sure you want to terminate all other sessions?"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm"
                >
                    Terminate All Other Sessions
                </button>
            </div>
        @endif

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($sessions as $session)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center">
                        <!-- Device Icon -->
                        <div class="flex-shrink-0">
                            @if ($session['platform'] === 'iOS' || $session['platform'] === 'Android')
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            @elseif ($session['platform'] === 'Windows' || $session['platform'] === 'Linux')
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @elseif ($session['platform'] === 'macOS')
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @else
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>

                        <!-- Session Info -->
                        <div class="ml-4">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $session['browser'] }} on {{ $session['platform'] }}
                                </p>
                                @if ($session['is_current'])
                                    <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded-full">
                                        Current
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $session['ip_address'] }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Last active: {{ $session['last_activity']->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if (!$session['is_current'])
                        <button
                            wire:click="terminateSession('{{ $session['id'] }}')"
                            wire:confirm="Are you sure you want to terminate this session?"
                            class="px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                        >
                            Terminate
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        @if (empty($sessions))
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-gray-600 dark:text-gray-400">No active sessions found</p>
            </div>
        @endif
    </div>

    <!-- Security Tips -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
            Security Tips
        </h3>
        <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
            <li>If you see any suspicious sessions, terminate them immediately and change your password.</li>
            <li>Enable two-factor authentication for an extra layer of security.</li>
            <li>Always log out from shared or public computers.</li>
        </ul>
    </div>
</div>
