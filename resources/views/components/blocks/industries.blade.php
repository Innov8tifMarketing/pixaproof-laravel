{{-- Industries: tabs on desktop, an accordion on phones. Each instance keeps
     its own Alpine state; the first industry starts open. --}}
@php
    $industries = collect($block->industries ?? [])->filter(fn ($industry) => filled($industry->tab_key))->values();
@endphp
<x-section :id="$block->anchor" class="bg-neutral-50" :eyebrow="$block->eyebrow" :title="$block->heading">
    {{-- Tabbed Interface --}}
    <div x-data="{ activeTab: '{{ $industries->first()?->tab_key }}' }">
        {{-- Tab Navigation - Desktop with underline indicator --}}
        <div class="mb-8 hidden gap-3 md:flex">
            @foreach ($industries as $industry)
                <button
                    @click="activeTab = '{{ $industry->tab_key }}'"
                    :class="activeTab === '{{ $industry->tab_key }}' ?
                        'border-primary-500 bg-primary-50 text-primary-700 border-b-2 border-b-primary-600' :
                        'border-neutral-200 bg-white text-neutral-600 hover:border-neutral-300 hover:bg-neutral-50'"
                    class="font-heading flex items-center gap-2 rounded-lg border px-5 py-3 text-sm font-semibold transition-colors"
                    type="button"
                >
                    @if (filled($industry->raw('icon')))
                        <x-dynamic-component :component="'heroicon-o-'.$industry->raw('icon')" class="h-4 w-4" />
                    @endif
                    {{ $industry->label }}
                </button>
            @endforeach
        </div>

        {{-- Mobile: Accordion --}}
        <div class="space-y-3 md:hidden">
            @foreach ($industries as $industry)
                <button
                    @click="activeTab = activeTab === '{{ $industry->tab_key }}' ? '' : '{{ $industry->tab_key }}'"
                    :class="activeTab === '{{ $industry->tab_key }}' ? 'border-primary-500 bg-primary-50' : 'border-neutral-200'"
                    class="flex w-full items-center justify-between rounded-lg border px-4 py-3 text-left font-semibold text-neutral-900 transition-colors"
                    type="button"
                >
                    <span class="flex items-center gap-2">
                        @if (filled($industry->raw('icon')))
                            <x-dynamic-component
                                :component="'heroicon-o-'.$industry->raw('icon')"
                                class="text-primary-600 h-4 w-4"
                            />
                        @endif
                        {{ $industry->label }}
                    </span>
                    <x-heroicon-o-chevron-down
                        class="h-5 w-5 transition-transform"
                        x-bind:class="activeTab === '{{ $industry->tab_key }}' ? 'rotate-180' : ''"
                    />
                </button>
                <div x-show="activeTab === '{{ $industry->tab_key }}'" x-collapse class="px-4 pb-4">
                    <div class="text-primary-600 mb-2 text-2xl font-bold">{{ $industry->headline }}</div>
                    <p class="mb-4 text-neutral-700">{{ $industry->body }}</p>
                    <x-check-list :items="$industry->bullets ?? []" compact />
                </div>
            @endforeach
        </div>

        {{-- Tab Content - Desktop --}}
        <div class="hidden md:block">
            @foreach ($industries as $industry)
                <div
                    x-show="activeTab === '{{ $industry->tab_key }}'"
                    x-transition
                    class="grid items-start gap-8 rounded-lg border border-neutral-200 bg-white p-8 lg:grid-cols-5"
                >
                    <div class="lg:col-span-3">
                        <div class="font-heading text-primary-600 mb-3 text-2xl font-bold">
                            {{ $industry->headline }}
                        </div>
                        @if (filled($industry->citation_source))
                            @php
                                $citation = collect([
                                    e($industry->citation_source),
                                    filled($industry->citation_title) ? '<em>'.e($industry->citation_title).'</em>' : null,
                                    filled($industry->citation_year) ? e($industry->citation_year) : null,
                                ])->filter()->implode(', ');
                            @endphp
                            <p class="mb-1 text-xs text-neutral-500">— {!! $citation !!}</p>
                        @endif
                        <h3 class="mb-4 text-xl font-semibold text-neutral-900">{{ $industry->subheading }}</h3>
                        <p class="mb-6 text-neutral-700">{{ $industry->body }}</p>
                        <x-check-list :items="$industry->bullets ?? []" />
                    </div>
                    <div class="flex justify-center lg:col-span-2">
                        <x-graphics.phone-mockup
                            variant="default"
                            size="lg"
                            :image="$industry->image?->url()"
                            :imageAlt="(string) $industry->image_alt"
                        />
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- And more --}}
    @if (filled($block->footnote))
        <p class="mt-8 text-center text-sm text-neutral-500">{{ $block->footnote }}</p>
    @endif
</x-section>
