{{-- Three steps, joined by arrows on desktop. --}}
<x-section
    :id="$block->anchor"
    class="bg-primary-50/50 border-primary-100 border-t"
    width="max-w-5xl"
    headerSpacing="mb-16"
    descriptionWidth="max-w-2xl"
    :eyebrow="$block->eyebrow"
    :title="$block->heading"
    :description="$block->intro"
>
    <x-graphics.solution-flow :steps="$block->steps ?? []" />
</x-section>
