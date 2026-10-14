{{-- Call to action: a closing heading, text and two buttons. --}}
<x-section
    :id="$block->anchor"
    class="from-primary-50 to-primary-100 relative overflow-hidden bg-gradient-to-b"
    width="max-w-4xl"
>
    <x-slot:background>
        {{-- Radial dot pattern --}}
        <div class="absolute inset-0 bg-[radial-gradient(#0ea5e915_1.5px,transparent_1.5px)] [mask-image:radial-gradient(ellipse_at_center,black_30%,transparent_70%)] bg-[size:2rem_2rem]"></div>
    </x-slot:background>

    <div class="text-center">
        <h2 class="font-heading mb-6 text-3xl font-bold text-neutral-900 md:text-4xl">{{ $block->heading }}</h2>
        @if (filled($block->text))
            <p class="mx-auto mb-10 max-w-2xl text-xl text-neutral-600">{{ $block->text }}</p>
        @endif

        <div class="flex flex-col justify-center gap-4 sm:flex-row">
            @if (filled($block->primary_label))
                <x-button :href="$block->primary_url" size="lg"> {{ $block->primary_label }} </x-button>
            @endif
            @if (filled($block->secondary_label))
                <x-button
                    :href="$block->secondary_url"
                    variant="outline"
                    size="lg"
                    class="border-neutral-300 text-neutral-700 hover:bg-neutral-100"
                >
                    {{ $block->secondary_label }}
                </x-button>
            @endif
        </div>

        @if (filled($block->trust_line))
            <p class="mt-10 text-sm text-neutral-500">{{ $block->trust_line }}</p>
        @endif
    </div>
</x-section>
