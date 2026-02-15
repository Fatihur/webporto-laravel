<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold">Experience</h1>
            <p class="text-zinc-500 mt-1">Manage your work history</p>
        </div>
        <a href="{{ route('admin.experiences.create') }}" wire:navigate
           class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="M12 5v14"/>
            </svg>
            Add Experience
        </a>
    </div>

    @if(count($experiences) > 0)
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold">Order</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Company</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Period</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($experiences as $experience)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $experience->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="moveUp({{ $experience->id }})"
                                        class="p-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors {{ $loop->first ? 'opacity-30 cursor-not-allowed' : '' }}"
                                    {{ $loop->first ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m18 15-6-6-6 6"/>
                                    </svg>
                                </button>
                                <span class="font-mono text-sm">{{ $experience->order }}</span>
                                <button wire:click="moveDown({{ $experience->id }})"
                                        class="p-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors {{ $loop->last ? 'opacity-30 cursor-not-allowed' : '' }}"
                                    {{ $loop->last ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold">{{ $experience->company }}</td>
                        <td class="px-6 py-4">{{ $experience->role }}</td>
                        <td class="px-6 py-4 text-zinc-500 text-sm">
                            {{ $experience->dateRange() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($experience->is_current)
                                <span class="px-3 py-1 bg-mint/10 text-mint text-xs font-bold rounded-full">Current</span>
                            @else
                                <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-xs font-bold rounded-full">Past</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.experiences.edit', $experience->id) }}" wire:navigate
                                   class="text-sm font-bold hover:text-mint transition-colors">Edit</a>
                                <button wire:click="delete({{ $experience->id }})"
                                        wire:confirm="Are you sure you want to delete this experience?"
                                        class="text-sm font-bold text-red-500 hover:text-red-600 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div
            class="text-center py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-400">
                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
            <p class="text-zinc-500 mb-4">No experience entries yet.</p>
            <a href="{{ route('admin.experiences.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform">
                Add First Experience
            </a>
        </div>
    @endif
</div>
