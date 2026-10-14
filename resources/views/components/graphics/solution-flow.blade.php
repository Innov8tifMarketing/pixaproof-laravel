{{--
    Solution Flow Graphic
    Shows the three-pillar flow: Secure Capture → Reliable Transmission → Instant Verification
    Horizontal timeline cards with arrow connectors (desktop) / stacked cards (mobile)

    Props:
    - steps: three rows (page builder grid) with title and text; the icons and colours stay fixed per step
--}}

@props(['steps' => []])

@php
    [$capture, $transmission, $verification] = array_pad(array_values(is_array($steps) ? $steps : iterator_to_array($steps)), 3, null);
@endphp

<div class="grid items-stretch gap-4 md:grid-cols-[1fr_auto_1fr_auto_1fr]">
    {{-- Step 1: Secure Capture --}}
    <div class="flex flex-col items-center rounded-2xl border border-neutral-200 bg-white p-6 text-center shadow-sm">
        {{-- Icon --}}
        <div class="bg-primary-500/10 flex h-14 w-14 items-center justify-center rounded-full">
            <svg class="text-primary-600 h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
            </svg>
        </div>
        {{-- Badge --}}
        <div class="bg-primary-500/10 text-primary-600 mt-4 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium">
            <span class="bg-primary-500 flex h-5 w-5 items-center justify-center rounded-full text-xs text-white">1</span>
            {{ $capture?->title }}
        </div>
        {{-- Description --}}
        <p class="mt-3 text-sm text-neutral-700">{{ $capture?->text }}</p>
    </div>

    {{-- Arrow 1→2 --}}
    <div class="hidden items-center md:flex">
        <svg class="text-primary-400 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
    </div>

    {{-- Step 2: Reliable Transmission --}}
    <div class="flex flex-col items-center rounded-2xl border border-neutral-200 bg-white p-6 text-center shadow-sm">
        {{-- Icon --}}
        <div class="bg-primary-500/10 flex h-14 w-14 items-center justify-center rounded-full">
            <svg class="text-primary-600 h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>
        {{-- Badge --}}
        <div class="bg-primary-500/10 text-primary-600 mt-4 inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium">
            <span class="bg-primary-500 flex h-5 w-5 items-center justify-center rounded-full text-xs text-white">2</span>
            {{ $transmission?->title }}
        </div>
        {{-- Description --}}
        <p class="mt-3 text-sm text-neutral-700">{{ $transmission?->text }}</p>
    </div>

    {{-- Arrow 2→3 --}}
    <div class="hidden items-center md:flex">
        <svg class="text-primary-400 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
        </svg>
    </div>

    {{-- Step 3: Instant Verification --}}
    <div class="flex flex-col items-center rounded-2xl border border-neutral-200 bg-white p-6 text-center shadow-sm">
        {{-- Icon --}}
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-500/10">
            <svg class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
            </svg>
        </div>
        {{-- Badge --}}
        <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-green-500/10 px-3 py-1 text-sm font-medium text-green-600">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-xs text-white">3</span>
            {{ $verification?->title }}
        </div>
        {{-- Description --}}
        <p class="mt-3 text-sm text-neutral-700">{{ $verification?->text }}</p>
    </div>
</div>
