{{-- Questions: an accordion, one answer open at a time. --}}
<x-section :id="$block->anchor" class="bg-white" width="max-w-3xl" :eyebrow="$block->eyebrow" :title="$block->heading">
    {{-- Accordion --}}
    <div x-data="{ openFaq: null }" class="space-y-3">
        @foreach ($block->items ?? [] as $index => $faq)
            <div
                class="overflow-hidden rounded-lg border border-neutral-200 transition-colors"
                :class="openFaq === {{ $index + 1 }} ? 'border-l-2 border-l-primary-500' : ''"
            >
                <button
                    @click="openFaq = openFaq === {{ $index + 1 }} ? null : {{ $index + 1 }}"
                    class="font-heading flex w-full items-center justify-between gap-4 bg-white px-6 py-4 text-left font-semibold text-neutral-900 transition-colors hover:bg-neutral-50"
                    type="button"
                >
                    {{ $faq->question }}
                    <x-heroicon-o-chevron-down
                        class="h-5 w-5 shrink-0 text-neutral-500 transition-transform"
                        x-bind:class="openFaq === {{ $index + 1 }} ? 'rotate-180' : ''"
                    />
                </button>
                <div
                    x-show="openFaq === {{ $index + 1 }}"
                    x-collapse
                    class="border-t border-neutral-200 bg-neutral-50 px-6 py-4"
                >
                    <p class="leading-relaxed text-neutral-700">{{ $faq->answer }}</p>
                </div>
            </div>
        @endforeach
    </div>
</x-section>
