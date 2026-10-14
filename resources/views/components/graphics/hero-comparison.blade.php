{{--
Hero Comparison Graphic
Shows three-panel comparison: Live Capture (Protected) vs Tampered Photos (Blocked) vs AI Generated (Detected)

Props:
- panels: three rows (page builder grid) with image (asset), image_alt, status, title, caption;
  styled in that order: green / red / amber
--}}

@props(['panels' => []])

@php
    [$live, $tampered, $generated] = array_pad(array_values(is_array($panels) ? $panels : iterator_to_array($panels)), 3, null);
@endphp

<div class="relative">
    {{-- Background glow --}}
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-1/2 left-1/4 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-green-500/10 blur-3xl"></div>
        <div class="bg-primary-500/10 absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-3/4 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-yellow-500/10 blur-3xl"></div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        {{-- Live Capture - Protected --}}
        <div class="group relative flex">
            <div class="absolute -inset-1 rounded-2xl bg-gradient-to-b from-green-500/20 to-transparent opacity-0 transition group-hover:opacity-100"></div>
            <div class="relative flex w-full flex-col items-center overflow-hidden rounded-xl border border-neutral-200 bg-white p-4">
                {{-- Status Badge --}}
                <div class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-full border border-green-300 bg-green-100 px-2 py-1">
                    <svg class="h-3 w-3 text-green-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-medium text-green-700">{{ $live?->status }}</span>
                </div>

                {{-- Phone mockup with camera overlay --}}
                <x-graphics.phone-mockup
                    size="md"
                    :image="$live?->image?->url()"
                    :imageAlt="(string) $live?->image_alt"
                >
                    <div class="flex h-full w-full flex-col">
                        {{-- Top bar with dark overlay --}}
                        <div class="flex items-center justify-between bg-black/70 px-3 pt-5 pb-1">
                            <svg class="h-3 w-3 text-white/70" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                            <span class="text-[0.5rem] font-medium text-white/90">PixaProof</span>
                            <div class="w-3"></div>
                        </div>
                        <div class="bg-black/70 px-3 pb-2">
                            <p class="text-center text-[0.45rem] font-medium text-white">
                                Capture photo of vehicle damage
                            </p>
                        </div>

                        {{-- Square viewfinder --}}
                        <div class="flex flex-1 items-center justify-center px-4">
                            <div class="relative aspect-square w-full">
                                {{-- Corner brackets --}}
                                <div class="absolute top-0 left-0 h-3 w-3 rounded-tl-sm border-t-2 border-l-2 border-white/80"></div>
                                <div class="absolute top-0 right-0 h-3 w-3 rounded-tr-sm border-t-2 border-r-2 border-white/80"></div>
                                <div class="absolute bottom-0 left-0 h-3 w-3 rounded-bl-sm border-b-2 border-l-2 border-white/80"></div>
                                <div class="absolute right-0 bottom-0 h-3 w-3 rounded-br-sm border-r-2 border-b-2 border-white/80"></div>
                            </div>
                        </div>

                        {{-- Capture button --}}
                        <div class="flex items-center justify-center py-3">
                            <div class="h-6 w-6 rounded-full border-[2.5px] border-white bg-white/20"></div>
                        </div>
                    </div>
                </x-graphics.phone-mockup>

                <div class="mt-4 text-center">
                    <h4 class="font-semibold text-neutral-900">{{ $live?->title }}</h4>
                    <p class="mt-1 text-xs text-neutral-500">{{ $live?->caption }}</p>
                </div>
            </div>
        </div>

        {{-- Tampered Photos - Blocked --}}
        <div class="group relative flex">
            <div class="absolute -inset-1 rounded-2xl bg-gradient-to-b from-red-500/20 to-transparent opacity-0 transition group-hover:opacity-100"></div>
            <div class="relative flex w-full flex-col items-center overflow-hidden rounded-xl border border-neutral-200 bg-white p-4">
                {{-- Status Badge --}}
                <div class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-full border border-red-300 bg-red-100 px-2 py-1">
                    <svg class="h-3 w-3 text-red-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <span class="text-xs font-medium text-red-700">{{ $tampered?->status }}</span>
                </div>

                {{-- Phone mockup with gallery image + error overlay --}}
                <x-graphics.phone-mockup
                    size="md"
                    :image="$tampered?->image?->url()"
                    :imageAlt="(string) $tampered?->image_alt"
                    variant="error"
                />

                <div class="mt-4 text-center">
                    <h4 class="font-semibold text-neutral-900">{{ $tampered?->title }}</h4>
                    <p class="mt-1 text-xs text-neutral-500">{{ $tampered?->caption }}</p>
                </div>
            </div>
        </div>

        {{-- AI Generated - Detected --}}
        <div class="group relative flex">
            <div class="absolute -inset-1 rounded-2xl bg-gradient-to-b from-yellow-500/20 to-transparent opacity-0 transition group-hover:opacity-100"></div>
            <div class="relative flex w-full flex-col items-center overflow-hidden rounded-xl border border-neutral-200 bg-white p-4">
                {{-- Status Badge --}}
                <div class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-full border border-amber-300 bg-amber-100 px-2 py-1">
                    <svg class="h-3 w-3 text-amber-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span class="text-xs font-medium text-amber-700">{{ $generated?->status }}</span>
                </div>

                {{-- Phone mockup with AI image + scanning overlay --}}
                <x-graphics.phone-mockup
                    size="md"
                    :image="$generated?->image?->url()"
                    :imageAlt="(string) $generated?->image_alt"
                    variant="scanning"
                />

                <div class="mt-4 text-center">
                    <h4 class="font-semibold text-neutral-900">{{ $generated?->title }}</h4>
                    <p class="mt-1 text-xs text-neutral-500">{{ $generated?->caption }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
