{{-- Cards with icons, staggered in, and a highlighted summary card. --}}
<x-section
    :id="$block->anchor"
    class="bg-white"
    headerSpacing="mb-16"
    :eyebrow="$block->eyebrow"
    :title="$block->heading"
    :description="$block->intro"
    intersect="visible = true; $nextTick(() => Motion.staggerFadeIn($el.querySelectorAll('[data-layer-card]'), { stagger: 0.1, delay: 0.15 }))"
>
    <div class="[&>div]:w-full [&>div]:md:w-[calc(50%-0.75rem)] mx-auto flex max-w-4xl flex-wrap justify-center gap-6">
        @foreach ($block->cards ?? [] as $card)
            <x-icon-card data-layer-card :icon="$card->raw('icon')" :title="$card->title">
                {{ $card->text }}
            </x-icon-card>
        @endforeach
    </div>

    {{-- Summary card --}}
    @if (filled($block->summary_title))
        <x-icon-card
            data-layer-card
            variant="inverted"
            :icon="$block->raw('summary_icon')"
            :title="$block->summary_title"
            class="mx-auto mt-6 max-w-4xl"
        >
            {{ $block->summary_text }}
        </x-icon-card>
    @endif
</x-section>
