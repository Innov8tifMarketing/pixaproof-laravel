{{-- Hero: badge, heading with rotating words, text, two buttons, and the
     three-phone comparison on large screens. --}}
<section
    @if (filled($block->anchor)) id="{{ $block->anchor }}" @endif
    class="relative isolate overflow-hidden px-4 pt-24 pb-20 lg:pt-28 lg:pb-28"
>
    {{-- Background: Gradient + grid + noise --}}
    <div class="noise-overlay absolute inset-0 -z-10">
        <div class="from-primary-50 to-primary-50/50 absolute inset-0 bg-gradient-to-br via-white"></div>
        {{-- Grid pattern with gradient fade --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#0ea5e918_1px,transparent_1px),linear-gradient(to_bottom,#0ea5e918_1px,transparent_1px)] [mask-image:radial-gradient(ellipse_at_center,black_40%,transparent_80%)] bg-[size:4rem_4rem]"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            {{-- Left: Copy --}}
            <div x-data="{ show: false }" x-init="setTimeout(() => (show = true), 100)">
                {{-- Eyebrow badge --}}
                @if (filled($block->badge))
                    <span
                        class="border-primary-500/40 bg-primary-500/10 font-heading text-primary-600 mb-6 inline-flex items-center rounded border px-3 py-1 text-xs font-medium tracking-wider uppercase transition-all duration-500"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        {{ $block->badge }}
                    </span>
                @endif

                {{-- Headline with rotating industry keyword --}}
                <h1
                    class="font-heading mb-6 text-4xl font-bold tracking-tight text-neutral-900 transition-all delay-100 duration-500 md:text-5xl lg:text-6xl"
                    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
                    {{ $block->heading }}
                    <span class="block">
                        @if (filled($block->rotating_words))
                            <x-rotating-text
                                :words="array_values($block->rotating_words)"
                                class="from-primary-500 to-primary-700 bg-gradient-to-r bg-clip-text text-transparent"
                            />
                        @endif
                        {{ $block->heading_end }}</span>
                </h1>

                {{-- Subheadline --}}
                @if (filled($block->intro))
                    <p
                        class="mb-10 max-w-xl text-lg leading-relaxed text-neutral-600 transition-all delay-200 duration-500"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        {{ $block->intro }}
                    </p>
                @endif

                {{-- Dual CTA --}}
                <div
                    class="flex flex-col gap-4 transition-all delay-300 duration-500 sm:flex-row"
                    :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
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
                            <x-heroicon-o-arrow-down class="ml-1 h-4 w-4" />
                        </x-button>
                    @endif
                </div>
            </div>

            {{-- Right: Visual --}}
            <div class="hidden lg:block">
                <x-graphics.hero-comparison :panels="$block->panels ?? []" />
            </div>
        </div>
    </div>
</section>
