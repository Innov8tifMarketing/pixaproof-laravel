{{-- The problem, scroll-scrubbed: pinned on desktop while the title, the
     three figures, the quote and the attack list play in turn
     (Motion.initScrubSection in resources/js/app.js, which binds the first
     [data-scrub-section] only and expects exactly three figures). --}}
@php
    $statColours = ['text-red-500', 'text-amber-300', null];
@endphp
<section @if (filled($block->anchor)) id="{{ $block->anchor }}" @endif data-scrub-section class="relative">
    {{-- Scroll runway: 6 phases × ~83vh each ≈ 500vh. Adjust if phases are added/removed. --}}
    <div class="h-auto md:h-[500vh]">
        {{-- Sticky viewport: pinned on desktop, normal flow on mobile --}}
        <div class="relative flex items-center justify-center overflow-hidden md:sticky md:top-16 md:h-[calc(100vh-4rem)]">
            {{-- Background color layer --}}
            <div data-scrub-bg class="absolute inset-0 bg-white"></div>

            {{-- Content viewport --}}
            <div data-scrub-viewport class="relative z-10 mx-auto w-full max-w-4xl px-4 text-neutral-900">
                {{-- Phase: Title --}}
                <div
                    data-scrub-phase="title"
                    class="py-16 text-center md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                >
                    <p class="text-primary-600 font-heading mb-4 text-sm font-semibold tracking-wider uppercase">
                        {{ $block->eyebrow }}
                    </p>
                    <h2 class="font-heading mb-6 text-3xl font-bold md:text-4xl lg:text-5xl">{{ $block->heading }}</h2>
                </div>

                {{-- Phase: Stats (one at a time on desktop, stacked on mobile) --}}
                @foreach (array_slice($block->stats ?? [], 0, 3) as $stat)
                    <div
                        data-scrub-stat="{{ $loop->index }}"
                        class="py-12 text-center md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <div class="font-heading mb-4 text-7xl font-bold {{ $statColours[$loop->index] }} md:text-8xl lg:text-9xl">
                            <span
                                data-scrub-counter
                                data-target="{{ (int) $stat->number }}"
                                data-suffix="{{ $stat->suffix }}"
                            >0{{ $stat->suffix }}</span>
                        </div>
                        <p class="mx-auto max-w-3xl text-lg opacity-90 md:text-xl">{{ $stat->text }}</p>
                        @if (filled($stat->source))
                            <p class="mt-3 text-xs opacity-50">{{ $stat->source }}</p>
                        @endif
                    </div>
                @endforeach

                {{-- Phase: Insight --}}
                <div
                    data-scrub-phase="insight"
                    class="py-12 md:absolute md:inset-0 md:flex md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                >
                    <div class="border-primary-400 relative mx-auto max-w-2xl border-l-4 pl-8">
                        <svg
                            class="text-primary-400/20 absolute -top-8 -left-4 h-16 w-16"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C9.591 11.69 11 13.198 11 15c0 1.857-1.344 3.5-3.2 3.5-1.296 0-2.541-.673-3.217-1.179zm12 0C15.553 16.227 15 15 15 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C21.591 11.69 23 13.198 23 15c0 1.857-1.344 3.5-3.2 3.5-1.296 0-2.541-.673-3.217-1.179z" />
                        </svg>
                        <p class="font-heading mb-4 text-2xl leading-snug font-bold md:text-3xl">{{ $block->quote }}</p>
                        <p class="text-lg leading-relaxed opacity-80">{{ $block->quote_text }}</p>
                    </div>
                </div>

                {{-- Phase: Gallery (attack vectors) --}}
                <div
                    data-scrub-phase="gallery"
                    class="py-12 md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                >
                    <p class="font-heading mb-3 text-center text-sm font-semibold tracking-wider text-red-400 uppercase">
                        {{ $block->gallery_eyebrow }}
                    </p>
                    <h3 class="font-heading mb-8 text-center text-2xl font-bold md:text-3xl">
                        {{ $block->gallery_heading }}
                    </h3>
                    <div class="[&>div]:w-full [&>div]:md:w-[calc(50%-0.5rem)] [&>div]:lg:w-[calc(33.333%-0.75rem)] flex w-full flex-wrap justify-center gap-4">
                        @foreach ($block->attacks ?? [] as $attack)
                            <div
                                data-scrub-card
                                class="rounded-lg border border-white/10 bg-white/5 p-5 md:will-change-[transform,opacity]"
                            >
                                @if (filled($attack->raw('icon')))
                                    <x-dynamic-component
                                        :component="'heroicon-o-'.$attack->raw('icon')"
                                        class="mb-3 h-6 w-6 text-red-400"
                                    />
                                @endif
                                <h4 class="font-heading mb-1 font-semibold">{{ $attack->title }}</h4>
                                <p class="text-sm opacity-80">{{ $attack->text }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
