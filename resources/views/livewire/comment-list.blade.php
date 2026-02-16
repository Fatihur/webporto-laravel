<div class="space-y-6">
    @if($comments && $comments->count() > 0)
        @foreach($comments as $comment)
            <!-- Main Comment -->
            <div class="flex gap-4">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-mint flex items-center justify-center text-zinc-950 font-black text-lg">
                        {{ strtoupper(substr($comment->name, 0, 1)) }}
                    </div>
                </div>

                <!-- Comment Content -->
                <div class="flex-1 min-w-0">
                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                            <div>
                                <span class="font-bold text-sm text-zinc-900 dark:text-white">{{ $comment->name }}</span>
                                <span class="text-xs text-zinc-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed">
                            {{ $comment->content }}
                        </p>
                        @if(!$comment->parent_id)
                            <button
                                x-data
                                @click="$dispatch('set-reply-to', { parentId: {{ $comment->id }}, name: '{{ addslashes($comment->name) }}' })"
                                class="mt-3 text-xs font-bold text-zinc-500 hover:text-mint transition-colors"
                            >
                                Reply
                            </button>
                        @endif
                    </div>

                    <!-- Replies -->
                    @if($comment->replies->count() > 0)
                        <div class="mt-4 ml-4 md:ml-8 space-y-4">
                            @foreach($comment->replies as $reply)
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-violet flex items-center justify-center text-zinc-950 font-bold text-sm">
                                            {{ strtoupper(substr($reply->name, 0, 1)) }}
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2 flex-wrap gap-1">
                                                <span class="font-bold text-sm text-zinc-900 dark:text-white">{{ $reply->name }}</span>
                                                <span class="text-xs text-zinc-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed">
                                                {{ $reply->content }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 font-medium">No comments yet. Be the first to comment!</p>
        </div>
    @endif
</div>
