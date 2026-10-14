{{-- Figures: a blue band of numbers that count up when it scrolls into view. --}}
<x-section :id="$block->anchor" class="bg-primary-600 noise-overlay relative" padding="py-16">
    <div class="grid grid-cols-2 gap-8 text-center lg:grid-cols-4">
        @foreach ($block->items ?? [] as $item)
            @if (filled($item->text))
                <x-stat :text="$item->text" :label="$item->label" />
            @else
                <x-stat :value="$item->number" :suffix="(string) $item->suffix" :label="$item->label" />
            @endif
        @endforeach
    </div>
</x-section>
