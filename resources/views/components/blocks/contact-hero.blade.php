{{-- Contact header: badge, heading, text, the email button and two short lines. --}}
<section
    @if (filled($block->anchor)) id="{{ $block->anchor }}" @endif
    class="relative isolate overflow-hidden px-4 pt-32 pb-20 lg:pt-40 lg:pb-28"
>
    <div class="absolute inset-0 -z-10">
        <div class="from-primary-50 to-primary-50/50 absolute inset-0 bg-gradient-to-br via-white"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#0ea5e910_1px,transparent_1px),linear-gradient(to_bottom,#0ea5e910_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-3xl text-center">
        @if (filled($block->badge))
            <div class="border-primary-500/40 bg-primary-500/10 mb-6 inline-flex items-center gap-2 rounded-full border px-4 py-2">
                <svg class="text-primary-600 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <span class="text-primary-600 text-sm font-medium">{{ $block->badge }}</span>
            </div>
        @endif
        <h1 class="mb-4 text-4xl font-bold text-neutral-900 md:text-5xl">{{ $block->heading }}</h1>
        @if (filled($block->text))
            <p class="mx-auto mb-10 max-w-xl text-lg text-neutral-600">{{ $block->text }}</p>
        @endif

        {{-- Email CTA --}}
        @if (filled($block->email))
            <a
                href="mailto:{{ $block->email }}"
                class="bg-accent-500 hover:bg-accent-600 inline-flex items-center gap-3 rounded-lg px-8 py-4 text-lg font-semibold text-white shadow-lg transition hover:shadow-xl"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                Email {{ $block->email }}
            </a>
        @endif

        <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-neutral-500">
            @if (filled($block->hours))
                <div class="flex items-center gap-2">
                    <svg class="text-primary-600 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $block->hours }}</span>
                </div>
            @endif
            @if (filled($block->response_time))
                <div class="flex items-center gap-2">
                    <svg class="text-primary-600 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $block->response_time }}</span>
                </div>
            @endif
        </div>
    </div>
</section>
