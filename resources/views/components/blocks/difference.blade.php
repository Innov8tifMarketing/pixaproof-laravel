{{-- Before and after: an ordinary upload beside PixaProof flagging it. --}}
<x-section
    :id="$block->anchor"
    class="relative border-y border-neutral-200 bg-neutral-50"
    width="max-w-6xl"
    headerSpacing="mb-16"
    descriptionWidth="max-w-2xl"
    :eyebrow="$block->eyebrow"
    :title="$block->heading"
    :description="$block->intro"
>
    <x-slot:background>
        {{-- Subtle dot pattern --}}
        <div class="absolute inset-0 bg-[radial-gradient(#94a3b820_1px,transparent_1px)] bg-[size:1.5rem_1.5rem]"></div>
    </x-slot:background>

    <x-graphics.prevention-visual
        :beforeHeading="$block->before_heading"
        :beforeResult="$block->before_result"
        :beforePoints="$block->before_points ?? []"
        :afterHeading="$block->after_heading"
        :afterResult="$block->after_result"
        :afterPoints="$block->after_points ?? []"
        :afterImage="$block->after_image?->url()"
        :afterImageAlt="$block->after_image_alt"
    />
</x-section>
