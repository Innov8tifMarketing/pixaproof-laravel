{{-- About: a heading, a timeline, certification cards and a link out. --}}
<x-section
    :id="$block->anchor"
    class="bg-neutral-100"
    headerSpacing="mb-16"
    :eyebrow="$block->eyebrow"
    :title="$block->heading"
    :description="$block->intro"
>
    {{-- Timeline --}}
    @if (filled($block->milestones))
        <div class="relative mb-16">
            {{-- Connecting line (desktop) with gradient pulse --}}
            <div class="from-primary-300 via-primary-500 to-primary-300 absolute top-6 right-0 left-0 hidden h-0.5 animate-[shimmer_3s_ease-in-out_infinite] bg-gradient-to-r bg-[length:200%_100%] md:block"></div>

            <div class="grid grid-cols-2 gap-6 md:grid-cols-6">
                @foreach ($block->milestones as $m)
                    <div class="relative text-center">
                        <div class="border-primary-500 relative z-10 mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full border-2 bg-white">
                            @if (filled($m->raw('icon')))
                                <x-dynamic-component
                                    :component="'heroicon-o-'.$m->raw('icon')"
                                    class="text-primary-600 h-5 w-5"
                                />
                            @endif
                        </div>
                        <div class="font-heading text-lg font-bold text-neutral-900">{{ $m->year }}</div>
                        <div class="mt-1 text-sm text-neutral-600">{{ $m->label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Certifications Row --}}
    <div class="grid gap-6 sm:grid-cols-3">
        @foreach ($block->certifications ?? [] as $certification)
            <x-icon-card
                layout="stacked"
                iconSize="lg"
                :icon="$certification->raw('icon')"
                :title="$certification->title"
            >
                {{ $certification->text }}
            </x-icon-card>
        @endforeach
    </div>

    @if (filled($block->link_label) && filled($block->link_url))
        <div class="mt-10 text-center">
            <a
                href="{{ $block->link_url }}"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary-600 hover:text-primary-700 inline-flex items-center gap-2 transition-colors"
            >
                {{ $block->link_label }}
                <x-heroicon-o-arrow-right class="h-4 w-4" />
            </a>
        </div>
    @endif
</x-section>
