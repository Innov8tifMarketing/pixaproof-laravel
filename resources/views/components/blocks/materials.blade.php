{{-- Downloadable materials: a heading and a card per material (the
     `downloadable_materials` collection: the ones picked, or all in order), each
     behind the request form. Renders nothing while no material has a file. --}}
@php
    $materials = \App\Support\DownloadableMaterials::picked($block->raw('materials'));
@endphp

@if ($materials->isNotEmpty())
    <x-section
        :id="$block->anchor"
        class="bg-white"
        :eyebrow="$block->eyebrow"
        :title="$block->heading"
        :description="$block->intro"
        width="max-w-6xl"
    >
        <div @class([
            'mx-auto grid gap-6',
            'max-w-md' => $materials->count() === 1,
            'max-w-4xl sm:grid-cols-2' => $materials->count() === 2,
            'sm:grid-cols-2 lg:grid-cols-3' => $materials->count() > 2,
        ])>
            @foreach ($materials as $material)
                @php($cover = $material->augmentedValue('cover')->value())
                <article class="group hover:border-primary-200 flex flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition hover:shadow-lg">
                    <div class="from-primary-50 relative flex aspect-[4/3] items-center justify-center overflow-hidden border-b border-neutral-100 bg-gradient-to-br to-neutral-100">
                        @if ($cover instanceof \Statamic\Contracts\Assets\Asset)
                            <img
                                src="{{ $cover->url() }}"
                                alt=""
                                loading="lazy"
                                class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.03]"
                            />
                        @else
                            <div class="flex h-28 w-22 flex-col justify-between rounded-md border border-neutral-200 bg-white p-3 shadow-md transition duration-500 group-hover:-translate-y-1">
                                <x-heroicon-o-document-text class="text-primary-500 h-7 w-7" />
                                <span class="bg-accent-500 self-start rounded px-1.5 py-0.5 text-[10px] font-bold text-white">PDF</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        @if (filled($material->get('kind')))
                            <p class="text-primary-600 mb-2 text-xs font-semibold tracking-wider uppercase">
                                {{ $material->get('kind') }}
                            </p>
                        @endif
                        <h3 class="font-heading mb-2 text-lg font-semibold text-neutral-900">
                            {{ $material->get('title') }}
                        </h3>
                        @if (filled($material->get('summary')))
                            <p class="mb-6 text-sm text-neutral-600">{{ $material->get('summary') }}</p>
                        @endif
                        <x-material-actions :material="$material" class="mt-auto" />
                    </div>
                </article>
            @endforeach
        </div>
    </x-section>
@endif
