{{--
Icon Card Component

Card with a heroicon tile, a title and a body paragraph (slot).

Usage:
<x-icon-card data-layer-card icon="camera" title="Capture Integrity">
    Flags virtual camera drivers, emulator sessions, and automated scripts.
</x-icon-card>
<x-icon-card layout="stacked" iconSize="lg" icon="shield-check" title="ISO 30107-3">
    iBeta Level 1 & 2 Liveness Compliance
</x-icon-card>
<x-icon-card variant="inverted" icon="bolt" title="All in Under 500ms">...</x-icon-card>

Props:
- icon: heroicon outline name without the `heroicon-o-` prefix (empty: an empty tile)
- title: card heading
- layout: 'inline' (icon beside title) | 'stacked' (centered icon above title)
- iconSize: 'md' (h-10 tile / h-5 icon) | 'lg' (h-12 tile / h-6 icon)
- variant: 'default' (white card, hover lift) | 'inverted' (primary fill, white text)
--}}

@props([
    'icon' => null,
    'title',
    'layout' => 'inline',
    'iconSize' => 'md',
    'variant' => 'default',
])

@php
    $inverted = $variant === 'inverted';
    $stacked = $layout === 'stacked';

    $cardClasses = $inverted
        ? 'bg-primary-600 rounded-lg p-6 text-white'
        : 'rounded-lg border border-neutral-200 bg-white p-6 transition-transform hover:-translate-y-1';

    if ($stacked) {
        $cardClasses .= ' text-center';
    }

    $tileClasses = $inverted ? 'bg-white/20' : 'bg-primary-100';
    $tileSize = $iconSize === 'lg' ? 'h-12 w-12' : 'h-10 w-10';
    $iconClasses = ($inverted ? 'text-white' : 'text-primary-600').($iconSize === 'lg' ? ' h-6 w-6' : ' h-5 w-5');
    $titleClasses = $inverted ? '' : ' text-neutral-900';
    $bodyClasses = $inverted ? 'text-primary-100' : ($stacked ? 'text-sm text-neutral-600' : 'text-neutral-600');
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if ($stacked)
        <div class="{{ $tileClasses }} {{ $tileSize }} mx-auto mb-4 flex items-center justify-center rounded-lg">
            @if (filled($icon))
                <x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $iconClasses }}" />
            @endif
        </div>
        <h3 class="font-heading mb-1 font-semibold{{ $titleClasses }}">{{ $title }}</h3>
    @else
        <div class="mb-4 flex items-center gap-3">
            <div class="{{ $tileClasses }} {{ $tileSize }} flex shrink-0 items-center justify-center rounded-lg">
                @if (filled($icon))
                    <x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $iconClasses }}" />
                @endif
            </div>
            <h3 class="font-heading font-semibold{{ $titleClasses }}">{{ $title }}</h3>
        </div>
    @endif
    <p class="{{ $bodyClasses }}">{{ $slot }}</p>
</div>
