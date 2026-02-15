<div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-20 bg-zinc-50 dark:bg-zinc-900/50 rounded-[3rem] px-8">
    @foreach($stats as $stat)
        <div class="text-center" wire:key="{{ $stat['label'] }}">
            <p class="text-4xl md:text-6xl font-black tracking-tighter mb-2">{{ $stat['display'] }}</p>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">{{ $stat['label'] }}</p>
        </div>
    @endforeach
</div>
