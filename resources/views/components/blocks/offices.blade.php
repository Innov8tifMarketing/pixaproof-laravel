{{-- Offices: a heading and the addresses in cards. --}}
<section @if (filled($block->anchor)) id="{{ $block->anchor }}" @endif class="bg-white py-20 lg:py-28">
    <div class="mx-auto max-w-5xl px-4">
        <div class="mb-12 text-center">
            <h2 class="font-heading mb-4 text-2xl font-bold text-neutral-900 md:text-3xl">{{ $block->heading }}</h2>
            @if (filled($block->intro))
                <p class="text-neutral-600">{{ $block->intro }}</p>
            @endif
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($block->offices ?? [] as $office)
                <div class="rounded-lg border border-neutral-200 p-6">
                    <div class="mb-3 flex items-center gap-2">
                        @if (filled($office->flag))
                            <span class="text-lg">{{ $office->flag }}</span>
                        @endif
                        <h3 class="font-heading font-semibold text-neutral-900">{{ $office->name }}</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-neutral-600">
                        {!! nl2br(e(trim((string) $office->address))) !!}
                    </p>
                </div>
            @endforeach
        </div>

        @if (filled($block->footnote))
            <p class="mt-10 text-center text-sm text-neutral-500">{{ $block->footnote }}</p>
        @endif
    </div>
</section>
