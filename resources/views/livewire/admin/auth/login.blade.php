<div class="w-full max-w-md px-6">
    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="{{ route('home') }}" class="text-3xl font-black tracking-tighter">ARTA</a>
        <h1 class="mt-6 text-2xl font-bold">Admin Login</h1>
        <p class="mt-2 text-sm text-zinc-500">Sign in to access the admin dashboard</p>
    </div>

    <!-- Login Form -->
    <form wire:submit="login" class="bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-800">
        @csrf

        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-bold mb-2">Email</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                placeholder="admin@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-bold mb-2">Password</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-6 flex items-center">
            <input
                type="checkbox"
                id="remember"
                wire:model="remember"
                class="w-4 h-4 rounded border-zinc-300 text-mint focus:ring-mint"
            >
            <label for="remember" class="ml-2 text-sm text-zinc-500">Remember me</label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 py-4 rounded-xl font-bold hover:scale-[1.02] transition-transform disabled:opacity-50"
        >
            <span wire:loading.remove>Sign In</span>
            <span wire:loading>Signing in...</span>
        </button>
    </form>

    <!-- Back to Site -->
    <div class="mt-8 text-center">
        <a href="{{ route('home') }}" wire:navigate class="text-sm text-zinc-500 hover:text-mint transition-colors">
            ← Back to website
        </a>
    </div>
</div>
