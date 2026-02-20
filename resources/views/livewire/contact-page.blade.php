<main class="pt-24 md:pt-32 pb-16 md:pb-20 px-4 sm:px-6 lg:px-12 max-w-7xl mx-auto" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16 lg:gap-20">
        <!-- Left Column: Contact Info -->
        <div class="transition-all duration-1000 transform" x-bind:class="show ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tighter mb-6 md:mb-10 leading-[1.1]">
                Let's start a Conversation.
            </h1>
            <p class="text-lg md:text-xl text-zinc-500 dark:text-zinc-400 mb-8 md:mb-12 max-w-md">
                I'm always open to new opportunities, collaborations, or just a friendly chat about design systems.
            </p>

            <div class="space-y-6 md:space-y-8">
                @if($siteContact && $siteContact->email)
                <a href="mailto:{{ $siteContact->email }}" class="flex items-center gap-4 md:gap-6 group hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center group-hover:bg-mint transition-colors shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" md:width="24" md:height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] md:text-xs font-black uppercase text-zinc-400 group-hover:text-mint transition-colors">{{ $siteContact->email_label ?: 'Email Me' }}</p>
                        <p class="text-base md:text-lg font-bold truncate">{{ $siteContact->email }}</p>
                    </div>
                </a>
                @endif

                @if($siteContact && ($siteContact->phone || $siteContact->whatsapp))
                <div class="flex items-center gap-4 md:gap-6 group hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center group-hover:bg-violet transition-colors shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" md:width="24" md:height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] md:text-xs font-black uppercase text-zinc-400 group-hover:text-violet transition-colors">
                            @if($siteContact->whatsapp)
                                {{ $siteContact->whatsapp_label ?: 'Chat with Me' }}
                            @else
                                {{ $siteContact->phone_label ?: 'Call Me' }}
                            @endif
                        </p>
                        <p class="text-base md:text-lg font-bold">
                            @if($siteContact->whatsapp)
                                {{ $siteContact->whatsapp }}
                            @else
                                {{ $siteContact->phone }}
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>

            @if($siteContact)
                @php
                    $socialLinks = $siteContact->getActiveSocialLinks();
                @endphp
                @if(count($socialLinks) > 0)
                <div class="mt-12 md:mt-20">
                    <p class="text-[10px] md:text-xs font-black uppercase text-zinc-400 mb-6 md:mb-8 tracking-widest">Connect Elsewhere</p>
                    <div class="flex gap-3 md:gap-4 flex-wrap">
                        @foreach($socialLinks as $key => $social)
                            @php
                                $iconSvg = '';
                                switch($key) {
                                    case 'github':
                                        $iconSvg = '<path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/>';
                                        break;
                                    case 'twitter':
                                        $iconSvg = '<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>';
                                        break;
                                    case 'linkedin':
                                        $iconSvg = '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>';
                                        break;
                                    case 'instagram':
                                        $iconSvg = '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>';
                                        break;
                                    case 'facebook':
                                        $iconSvg = '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>';
                                        break;
                                    case 'youtube':
                                        $iconSvg = '<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17z"/><path d="m10 15 5-3-5-3z"/>';
                                        break;
                                    case 'tiktok':
                                        $iconSvg = '<path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>';
                                        break;
                                    default:
                                        $iconSvg = '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>';
                                }
                            @endphp
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 md:w-12 md:h-12 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:border-mint hover:-translate-y-1 hover:shadow-lg transition-all duration-300"
                               aria-label="{{ $social['label'] ?: ucfirst($key) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" md:width="20" md:height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $iconSvg !!}
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>

        <!-- Right Column: Livewire Contact Form -->
        <div class="bg-zinc-50 dark:bg-zinc-900 p-6 sm:p-8 md:p-12 rounded-3xl md:rounded-[3rem] transition-all duration-1000 delay-300 transform" x-bind:class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'">
            <livewire:contact-form />
        </div>
    </div>
</main>
