{{-- Comparison table: a table on desktop, stacked cards on phones. A cell of
     `yes` shows a tick and `no` a cross (on phones: "Yes" / "No"); anything
     else shows as text. --}}
@php
    $mark = fn ($value) => in_array(strtolower(trim((string) $value)), ['yes', 'no'], true) ? strtolower(trim((string) $value)) : null;
@endphp
<x-section
    :id="$block->anchor"
    class="bg-neutral-50"
    width="max-w-5xl"
    :eyebrow="$block->eyebrow"
    :title="$block->heading"
>
    {{-- Desktop Table --}}
    <div class="hidden overflow-hidden rounded-lg border border-neutral-200 md:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-neutral-100">
                    <th class="font-heading px-6 py-4 text-left font-semibold text-neutral-900">
                        {{ $block->capability_heading }}
                    </th>
                    <th class="font-heading px-6 py-4 text-center font-semibold text-neutral-500">
                        {{ $block->manual_heading }}
                    </th>
                    <th class="font-heading text-primary-700 bg-primary-100 border-l-primary-500 border-l-2 px-6 py-4 text-center font-semibold">
                        {{ $block->pixaproof_heading }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @foreach ($block->rows ?? [] as $row)
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">{{ $row->capability }}</td>
                        @switch ($mark($row->manual))
                            @case ('yes')
                                <td class="px-6 py-4 text-center">
                                    <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                                </td>
                                @break
                            @case ('no')
                                <td class="px-6 py-4 text-center">
                                    <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                                </td>
                                @break
                            @default
                                <td class="px-6 py-4 text-center text-neutral-500">{{ $row->manual }}</td>
                        @endswitch
                        @switch ($mark($row->pixaproof))
                            @case ('yes')
                                <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                                    <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                                </td>
                                @break
                            @case ('no')
                                <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                                    <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                                </td>
                                @break
                            @default
                                <td class="bg-primary-50 border-l-primary-500 text-primary-700 border-l-2 px-6 py-4 text-center font-semibold">
                                    {{ $row->pixaproof }}
                                </td>
                        @endswitch
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile: Stacked cards --}}
    <div
        class="space-y-4 md:hidden"
        x-intersect.once="$nextTick(() => Motion.staggerFadeIn($el.children, { stagger: 0.06, y: 15 }))"
    >
        @foreach ($block->rows ?? [] as $row)
            <div class="rounded-lg border border-neutral-200 p-4">
                <div class="mb-3 font-semibold text-neutral-900">{{ $row->capability }}</div>
                <div class="grid grid-cols-2 gap-2 text-center text-sm">
                    <div>
                        <div class="mb-1 text-xs text-neutral-500">{{ $block->manual_heading }}</div>
                        <div class="text-neutral-600">
                            {{ $mark($row->manual) ? ucfirst($mark($row->manual)) : $row->manual }}
                        </div>
                    </div>
                    <div class="bg-primary-50 rounded p-1">
                        <div class="text-primary-600 mb-1 text-xs font-semibold">{{ $block->pixaproof_heading }}</div>
                        <div class="text-primary-700 font-semibold">
                            {{ $mark($row->pixaproof) ? ucfirst($mark($row->pixaproof)) : $row->pixaproof }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-section>
