{{-- Trust strip: a label over a row of icons with short claims, staggered in. --}}
<section
    @if (filled($block->anchor)) id="{{ $block->anchor }}" @endif
    class="border-y border-neutral-200 bg-neutral-100 py-5"
    x-data="{ visible: false }"
    x-intersect.once="
        visible = true;
        $nextTick(() => Motion.staggerFadeIn($el.querySelectorAll('[data-trust-item]'), { stagger: 0.06, y: 10 }));
    "
>
    <div class="mx-auto max-w-7xl px-4">
        @if (filled($block->label))
            <p class="text-primary-700 mb-3 mb-4 text-center text-xs font-bold tracking-wider uppercase">
                {{ $block->label }}
            </p>
        @endif
        <div class="font-heading flex items-center justify-between gap-8 overflow-x-auto py-1 text-sm font-medium text-neutral-600">
            @foreach ($block->items ?? [] as $item)
                <div data-trust-item class="flex items-center gap-2 whitespace-nowrap">
                    @if (filled($item->raw('icon')))
                        <x-dynamic-component
                            :component="'heroicon-o-'.$item->raw('icon')"
                            class="text-primary-600 h-5 w-5 shrink-0"
                        />
                    @endif
                    <span>{{ $item->label }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
