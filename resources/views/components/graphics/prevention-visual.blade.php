{{--
Prevention Visual Component
Shows side-by-side comparison of Traditional vs PixaProof approach with phone mockups.
The phone screens are fixed illustrations; the copy around them comes from the props.

Props:
- beforeHeading / afterHeading: the label over each phone
- beforeResult / afterResult: the pill under each phone
- beforePoints / afterPoints: list<string> for the boxes underneath
- afterImage / afterImageAlt: the photo behind the PixaProof phone's screen
--}}

@props([
    'beforeHeading' => null,
    'beforeResult' => null,
    'beforePoints' => [],
    'afterHeading' => null,
    'afterResult' => null,
    'afterPoints' => [],
    'afterImage' => null,
    'afterImageAlt' => '',
])

<div class="relative">
    {{-- Background glow effects --}}
    <div class="absolute top-1/2 left-1/4 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-red-500/10 blur-3xl"></div>
    <div class="absolute top-1/2 right-1/4 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-green-500/10 blur-3xl"></div>

    <div class="grid gap-8 md:grid-cols-2">
        {{-- Traditional Approach --}}
        <div class="relative">
            <div class="mb-4 flex items-center justify-center gap-2">
                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                <span class="text-sm font-medium text-red-600">{{ $beforeHeading }}</span>
            </div>

            {{-- Phone mockup with gallery upload flow --}}
            <div class="flex justify-center">
                <div class="relative">
                    <x-graphics.phone-mockup size="xl">
                        <div class="flex h-full w-full flex-col bg-white">
                            {{-- Status bar --}}
                            <div class="flex items-center justify-between px-4 pt-6 pb-2 text-xs text-neutral-400">
                                <span>9:41</span>
                                <span class="text-xs font-medium text-neutral-700">Gallery</span>
                            </div>

                            {{-- Gallery grid --}}
                            <div class="flex-1 px-3 pt-2">
                                <div class="grid grid-cols-3 gap-2">
                                    {{-- Thumbnail 1 --}}
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-neutral-100">
                                        <svg class="h-5 w-5 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159M15 12l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21z" />
                                        </svg>
                                    </div>
                                    {{-- Thumbnail 2 --}}
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-neutral-100">
                                        <svg class="h-5 w-5 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159M15 12l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21z" />
                                        </svg>
                                    </div>
                                    {{-- Thumbnail 3 (selected — the edited photo) --}}
                                    <div class="border-primary-500 relative flex aspect-square items-center justify-center rounded-lg border-2 bg-neutral-100">
                                        <svg class="h-5 w-5 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159M15 12l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21z" />
                                        </svg>
                                        {{-- Selection checkmark --}}
                                        <div class="bg-primary-500 absolute -top-1 -right-1 flex h-4.5 w-4.5 items-center justify-center rounded-full">
                                            <svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div>
                                    </div>
                                    {{-- Thumbnail 4 --}}
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-neutral-100">
                                        <svg class="h-5 w-5 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159M15 12l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21z" />
                                        </svg>
                                    </div>
                                    {{-- Thumbnail 5 --}}
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-neutral-100">
                                        <svg class="h-5 w-5 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159M15 12l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21z" />
                                        </svg>
                                    </div>
                                    {{-- Thumbnail 6 --}}
                                    <div class="flex aspect-square items-center justify-center rounded-lg bg-neutral-100">
                                        <svg class="h-5 w-5 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159M15 12l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21z" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Warning badge — subtle, easy to miss --}}
                                <div class="mt-2.5 flex items-center justify-center gap-1.5 rounded-md bg-amber-50 px-2.5 py-1.5">
                                    <svg class="h-3.5 w-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                    <span class="text-[0.65rem] text-amber-500">Pre-edited photo</span>
                                </div>
                            </div>

                            {{-- Upload button --}}
                            <div class="px-3 pb-2">
                                <div class="bg-primary-500 rounded-lg py-2.5 text-center text-xs font-semibold text-white">
                                    Upload Photo
                                </div>
                            </div>

                            {{-- Success confirmation — deceptive calm --}}
                            <div class="flex items-center justify-center gap-1.5 bg-green-50 px-3 py-2">
                                <svg class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-[0.7rem] font-medium text-green-600">Submitted successfully</span>
                            </div>
                        </div>
                    </x-graphics.phone-mockup>

                    {{-- Result indicator --}}
                    <div class="mt-4 flex justify-center">
                        <div class="flex items-center gap-2 rounded-full bg-red-500/20 px-3 py-1.5 text-xs text-red-600">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <span>{{ $beforeResult }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Issues list --}}
            <div class="mt-6 space-y-2 rounded-xl border border-red-500/20 bg-red-500/5 p-4">
                @foreach ($beforePoints as $point)
                    <div class="flex items-center gap-2 text-sm text-neutral-700">
                        <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>{{ $point }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PixaProof Approach --}}
        <div class="relative">
            <div class="mb-4 flex items-center justify-center gap-2">
                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                <span class="text-sm font-medium text-green-600">{{ $afterHeading }}</span>
            </div>

            {{-- Phone showing fraud blocked --}}
            <div class="flex justify-center">
                <div class="relative">
                    <x-graphics.phone-mockup size="xl" :image="$afterImage" :imageAlt="(string) $afterImageAlt">
                        <div class="flex h-full flex-col">
                            {{-- Status bar --}}
                            <div class="flex items-center justify-between px-4 pt-6 pb-2 text-xs text-neutral-400">
                                <span>9:41</span>
                                <div class="flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    <span class="text-[0.6rem] text-green-500">Secured</span>
                                </div>
                            </div>

                            {{-- Analysis results overlay --}}
                            <div class="flex flex-1 flex-col items-center justify-center bg-neutral-900/60 p-5 backdrop-blur-sm">
                                {{-- Scanning animation ring --}}
                                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full border-2 border-amber-400 bg-amber-400/10">
                                    <svg class="h-7 w-7 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                </div>
                                <p class="text-center text-sm font-semibold text-white">Gallery Upload Detected</p>
                                <p class="mt-1 text-center text-[0.7rem] text-neutral-400">
                                    Capture environment not verified
                                </p>

                                {{-- Forensic detail chips --}}
                                <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                                    <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[0.55rem] text-amber-300">No PIEA signature</span>
                                    <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[0.55rem] text-amber-300">Non-live source</span>
                                </div>
                            </div>

                            {{-- SDK badge --}}
                            <div class="bg-neutral-100 p-2.5">
                                <div class="flex items-center justify-center gap-1.5 text-[0.6rem] text-neutral-600">
                                    <svg class="text-primary-600 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    <span>PixaProof SDK</span>
                                </div>
                            </div>
                        </div>
                    </x-graphics.phone-mockup>

                    {{-- Result indicator --}}
                    <div class="mt-4 flex justify-center">
                        <div class="flex items-center gap-2 rounded-full bg-green-500/20 px-3 py-1.5 text-xs text-green-600">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $afterResult }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Benefits list --}}
            <div class="mt-6 space-y-2 rounded-xl border border-green-500/20 bg-green-500/5 p-4">
                @foreach ($afterPoints as $point)
                    <div class="flex items-center gap-2 text-sm text-neutral-700">
                        <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>{{ $point }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
