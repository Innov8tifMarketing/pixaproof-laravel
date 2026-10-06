@extends('layout')

@section('content')
    {{-- Section 1: Hero --}}
    <section class="relative isolate overflow-hidden px-4 pt-24 pb-20 lg:pt-28 lg:pb-28">
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
                    <span
                        class="border-primary-500/40 bg-primary-500/10 font-heading text-primary-600 mb-6 inline-flex items-center rounded border px-3 py-1 text-xs font-medium tracking-wider uppercase transition-all duration-500"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        Now Available
                    </span>

                    {{-- Headline with rotating industry keyword --}}
                    <h1
                        class="font-heading mb-6 text-4xl font-bold tracking-tight text-neutral-900 transition-all delay-100 duration-500 md:text-5xl lg:text-6xl"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        Stop Fake Images From Entering Your
                        <span class="block"
                            ><x-rotating-text
                                :words="['Item Lending', 'Insurance Claims', 'Product Delivery']"
                                class="from-primary-500 to-primary-700 bg-gradient-to-r bg-clip-text text-transparent"
                            />
                            Workflows.</span>
                    </h1>

                    {{-- Subheadline --}}
                    <p
                        class="mb-10 max-w-xl text-lg leading-relaxed text-neutral-600 transition-all delay-200 duration-500"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        Fraudulent images cost enterprises millions in false transactions. PixaProof's SDK secures
                        photos at the moment of capture — cryptographically signed, tamper-proof, and verified before
                        they ever reach your system.
                    </p>

                    {{-- Dual CTA --}}
                    <div
                        class="flex flex-col gap-4 transition-all delay-300 duration-500 sm:flex-row"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    >
                        <x-button href="/contact" size="lg"> Book a Demo </x-button>
                        <x-button
                            href="#how-it-works"
                            variant="outline"
                            size="lg"
                            class="border-neutral-300 text-neutral-700 hover:bg-neutral-100"
                        >
                            See How It Works
                            <x-heroicon-o-arrow-down class="ml-1 h-4 w-4" />
                        </x-button>
                    </div>
                </div>

                {{-- Right: Visual --}}
                <div class="hidden lg:block">
                    <x-graphics.hero-comparison />
                </div>
            </div>
        </div>
    </section>

    {{-- Section 2: Trust Strip --}}
    <section
        class="border-y border-neutral-200 bg-neutral-100 py-5"
        x-data="{ visible: false }"
        x-intersect.once="
            visible = true;
            $nextTick(() => Motion.staggerFadeIn($el.querySelectorAll('[data-trust-item]'), { stagger: 0.06, y: 10 }));
        "
    >
        <div class="mx-auto max-w-7xl px-4">
            <p class="text-primary-700 mb-3 mb-4 text-center text-xs font-bold tracking-wider uppercase">
                Built by one of ASEAN's top eKYC providers
            </p>
            <div class="font-heading flex items-center justify-between gap-8 overflow-x-auto py-1 text-sm font-medium text-neutral-600">
                <div data-trust-item class="flex items-center gap-2 whitespace-nowrap">
                    <x-heroicon-o-shield-check class="text-primary-600 h-5 w-5 shrink-0" />
                    <span>ISO 30107-3 Compliant</span>
                </div>
                <div data-trust-item class="flex items-center gap-2 whitespace-nowrap">
                    <x-heroicon-o-light-bulb class="text-primary-600 h-5 w-5 shrink-0" />
                    <span>3 Patents Granted</span>
                </div>
                <div data-trust-item class="flex items-center gap-2 whitespace-nowrap">
                    <x-heroicon-o-clock class="text-primary-600 h-5 w-5 shrink-0" />
                    <span>Since 2011</span>
                </div>
                <div data-trust-item class="flex items-center gap-2 whitespace-nowrap">
                    <x-heroicon-o-finger-print class="text-primary-600 h-5 w-5 shrink-0" />
                    <span>10M+ Verifications</span>
                </div>
                <div data-trust-item class="flex items-center gap-2 whitespace-nowrap">
                    <x-heroicon-o-globe-asia-australia class="text-primary-600 h-5 w-5 shrink-0" />
                    <span>10 ASEAN Countries</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 3: The Challenge — Scroll-Scrubable --}}
    <section id="challenge" data-scrub-section class="relative">
        {{-- Scroll runway: 6 phases × ~83vh each ≈ 500vh. Adjust if phases are added/removed. --}}
        <div class="h-auto md:h-[500vh]">
            {{-- Sticky viewport: pinned on desktop, normal flow on mobile --}}
            <div class="relative flex items-center justify-center overflow-hidden md:sticky md:top-16 md:h-[calc(100vh-4rem)]">
                {{-- Background color layer --}}
                <div data-scrub-bg class="absolute inset-0 bg-white"></div>

                {{-- Content viewport --}}
                <div data-scrub-viewport class="relative z-10 mx-auto w-full max-w-4xl px-4 text-neutral-900">
                    {{-- Phase: Title --}}
                    <div
                        data-scrub-phase="title"
                        class="py-16 text-center md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <p class="text-primary-600 font-heading mb-4 text-sm font-semibold tracking-wider uppercase">
                            The Problem
                        </p>
                        <h2 class="font-heading mb-6 text-3xl font-bold md:text-4xl lg:text-5xl">
                            Every Manipulated Photo That Slips Through Has a Price
                        </h2>
                    </div>

                    {{-- Phase: Stats (one at a time on desktop, stacked on mobile) --}}
                    <div
                        data-scrub-stat="0"
                        class="py-12 text-center md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <div class="font-heading mb-4 text-7xl font-bold text-red-500 md:text-8xl lg:text-9xl">
                            <span data-scrub-counter data-target="90" data-suffix="%">0%</span>
                        </div>
                        <p class="mx-auto max-w-3xl text-lg opacity-90 md:text-xl">
                            of online content will be AI-generated by 2026. Without proof of origin and verification,
                            nothing can be trusted. The question isn't whether you'll receive a fake — it's whether you
                            have systems in place to catch it.
                        </p>
                        <p class="mt-3 text-xs opacity-50">Source: Europol Innovation Lab; Nina Schick</p>
                    </div>

                    <div
                        data-scrub-stat="1"
                        class="py-12 text-center md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <div class="font-heading mb-4 text-7xl font-bold text-amber-300 md:text-8xl lg:text-9xl">
                            <span data-scrub-counter data-target="64" data-suffix="%">0%</span>
                        </div>
                        <p class="mx-auto max-w-3xl text-lg opacity-90 md:text-xl">
                            of claims handlers flag generative AI as a growing fraud risk. Claims backed by synthetic
                            photos are already slipping through — and adjusters can't tell the difference.
                        </p>
                        <p class="mt-3 text-xs opacity-50">Source: ITIJ Claims Handler Survey</p>
                    </div>

                    <div
                        data-scrub-stat="2"
                        class="py-12 text-center md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <div class="font-heading mb-4 text-7xl font-bold md:text-8xl lg:text-9xl">
                            <span data-scrub-counter data-target="30" data-suffix="%">0%</span>
                        </div>
                        <p class="mx-auto max-w-3xl text-lg opacity-90 md:text-xl">
                            of enterprises will drop standalone identity verification by 2026. When the source image
                            itself is fabricated, downstream verification has nothing real to verify. Verification must
                            begin at the point of capture.
                        </p>
                        <p class="mt-3 text-xs opacity-50">Source: Gartner, Feb 2024</p>
                    </div>

                    {{-- Phase: Insight --}}
                    <div
                        data-scrub-phase="insight"
                        class="py-12 md:absolute md:inset-0 md:flex md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <div class="border-primary-400 relative mx-auto max-w-2xl border-l-4 pl-8">
                            <svg
                                class="text-primary-400/20 absolute -top-8 -left-4 h-16 w-16"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C9.591 11.69 11 13.198 11 15c0 1.857-1.344 3.5-3.2 3.5-1.296 0-2.541-.673-3.217-1.179zm12 0C15.553 16.227 15 15 15 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C21.591 11.69 23 13.198 23 15c0 1.857-1.344 3.5-3.2 3.5-1.296 0-2.541-.673-3.217-1.179z" />
                            </svg>
                            <p class="font-heading mb-4 text-2xl leading-snug font-bold md:text-3xl">
                                Verifying what is in an image is no longer enough — you must verify its origin.
                            </p>
                            <p class="text-lg leading-relaxed opacity-80">
                                Organizations currently store "blind" media with no verifiable link to the actual
                                capture environment — leading to massive potential liability.
                            </p>
                        </div>
                    </div>

                    {{-- Phase: Gallery (attack vectors) --}}
                    <div
                        data-scrub-phase="gallery"
                        class="py-12 md:absolute md:inset-0 md:flex md:flex-col md:items-center md:justify-center md:py-0 md:will-change-[transform,opacity]"
                    >
                        <p class="font-heading mb-3 text-center text-sm font-semibold tracking-wider text-red-400 uppercase">
                            Attack Vectors
                        </p>
                        <h3 class="font-heading mb-8 text-center text-2xl font-bold md:text-3xl">
                            5 Ways Fraudsters Exploit Your Photo Pipeline
                        </h3>
                        <div class="[&>div]:w-full [&>div]:md:w-[calc(50%-0.5rem)] [&>div]:lg:w-[calc(33.333%-0.75rem)] flex w-full flex-wrap justify-center gap-4">
                            <div
                                data-scrub-card
                                class="rounded-lg border border-white/10 bg-white/5 p-5 md:will-change-[transform,opacity]"
                            >
                                <x-heroicon-o-photo class="mb-3 h-6 w-6 text-red-400" />
                                <h4 class="font-heading mb-1 font-semibold">Gallery uploads instead of live photos</h4>
                                <p class="text-sm opacity-80">
                                    Users upload pre-edited images from their camera roll — apps can't tell the
                                    difference
                                </p>
                            </div>
                            <div
                                data-scrub-card
                                class="rounded-lg border border-white/10 bg-white/5 p-5 md:will-change-[transform,opacity]"
                            >
                                <x-heroicon-o-cpu-chip class="mb-3 h-6 w-6 text-red-400" />
                                <h4 class="font-heading mb-1 font-semibold">AI-generated documents and deepfakes</h4>
                                <p class="text-sm opacity-80">
                                    Synthetic IDs, fake damage photos, and AI-altered evidence are now trivial to
                                    produce
                                </p>
                            </div>
                            <div
                                data-scrub-card
                                class="rounded-lg border border-white/10 bg-white/5 p-5 md:will-change-[transform,opacity]"
                            >
                                <x-heroicon-o-video-camera class="mb-3 h-6 w-6 text-red-400" />
                                <h4 class="font-heading mb-1 font-semibold">Virtual camera injection</h4>
                                <p class="text-sm opacity-80">
                                    Software-based virtual cameras bypass standard capture interfaces, injecting
                                    pre-recorded or AI-generated content
                                </p>
                            </div>
                            <div
                                data-scrub-card
                                class="rounded-lg border border-white/10 bg-white/5 p-5 md:will-change-[transform,opacity]"
                            >
                                <x-heroicon-o-computer-desktop class="mb-3 h-6 w-6 text-red-400" />
                                <h4 class="font-heading mb-1 font-semibold">Headless emulator attacks</h4>
                                <p class="text-sm opacity-80">
                                    Fraudsters use emulators to inject thousands of synthetic images simultaneously,
                                    bypassing device checks entirely
                                </p>
                            </div>
                            <div
                                data-scrub-card
                                class="rounded-lg border border-white/10 bg-white/5 p-5 md:will-change-[transform,opacity]"
                            >
                                <x-heroicon-o-map-pin class="mb-3 h-6 w-6 text-red-400" />
                                <h4 class="font-heading mb-1 font-semibold">Spoofed location and timestamp data</h4>
                                <p class="text-sm opacity-80">
                                    GPS and date metadata can be changed with free tools before submission
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 4: Before/After Comparison --}}
    <x-section
        class="relative border-y border-neutral-200 bg-neutral-50"
        width="max-w-6xl"
        headerSpacing="mb-16"
        descriptionWidth="max-w-2xl"
        eyebrow="The Difference"
        title="One Layer of Protection Changes Everything"
        description="See what happens when you secure the capture point — the one moment traditional systems cannot verify."
    >
        <x-slot:background>
            {{-- Subtle dot pattern --}}
            <div class="absolute inset-0 bg-[radial-gradient(#94a3b820_1px,transparent_1px)] bg-[size:1.5rem_1.5rem]"></div>
        </x-slot:background>

        <x-graphics.prevention-visual />
    </x-section>

    {{-- Section 5: Three Layers of Protection --}}
    <x-section
        id="solution"
        class="bg-white"
        headerSpacing="mb-16"
        eyebrow="The Solution"
        title="Three Layers of Protection in Every Image"
        description="PixaProof doesn't just detect fraud after the fact — it prevents it at the source, then verifies every image through three independent checks."
        intersect="visible = true; $nextTick(() => Motion.staggerFadeIn($el.querySelectorAll('[data-layer-card]'), { stagger: 0.1, delay: 0.15 }))"
    >
        <div class="[&>div]:w-full [&>div]:md:w-[calc(50%-0.75rem)] mx-auto flex max-w-4xl flex-wrap justify-center gap-6">
            <x-icon-card data-layer-card icon="camera" title="Capture Integrity">
                Flags virtual camera drivers, emulator sessions, and automated scripts. Gallery uploads, screenshots,
                and file injections are detected and flagged — with full capture environment forensics on every
                submission.
            </x-icon-card>

            <x-icon-card data-layer-card icon="lock-closed" title="Source Protection">
                Images are sealed with an invisible, tamper-evident digital watermark. The system binds media to its
                environmental data — making unauthorized edits or alterations significantly harder.
            </x-icon-card>

            <x-icon-card data-layer-card icon="magnifying-glass" title="AI & Tampering Detection">
                Identifies synthetic media injection, AI-generated content, and pixel-level manipulation that manual
                review would miss.
            </x-icon-card>
        </div>

        {{-- Summary card --}}
        <x-icon-card
            data-layer-card
            variant="inverted"
            icon="bolt"
            title="All in Under 500ms"
            class="mx-auto mt-6 max-w-4xl"
        >
            Every image runs through all three checks automatically — results returned via Web SDK and API before your
            user finishes the next step.
        </x-icon-card>
    </x-section>

    {{-- Section 6: How It Works --}}
    <x-section
        id="how-it-works"
        class="bg-primary-50/50 border-primary-100 border-t"
        width="max-w-5xl"
        headerSpacing="mb-16"
        descriptionWidth="max-w-2xl"
        eyebrow="How It Works"
        title="Three Steps to Verified Evidence"
        description="Embed our Web SDK in your application. From the moment of capture to your API response — PixaProof secures, transmits, and verifies every image automatically."
    >
        <x-graphics.solution-flow />
    </x-section>

    {{-- Section 7: Video Demos --}}
    <x-section
        id="demos"
        class="bg-white"
        descriptionWidth="max-w-2xl"
        eyebrow="See It In Action"
        title="PixaProof Detects Every Attack Vector"
        description="Watch how PixaProof identifies genuine captures and flags simulated sources in real time."
    >
        <div
            x-data="{
                open: false,
                activeVideo: null,
                activeLabel: '',
                activeResult: '',
                activeGenuine: false,
                play(video) {
                    this.activeVideo = video.file;
                    this.activeLabel = video.label;
                    this.activeResult = video.result;
                    this.activeGenuine = video.genuine;
                    this.open = true;
                    this.$nextTick(() => {
                        this.$refs.lightboxVideo && this.$refs.lightboxVideo.play();
                    });
                },
                close() {
                    this.open = false;
                    this.$refs.lightboxVideo && this.$refs.lightboxVideo.pause();
                    this.activeVideo = null;
                },
            }"
            @keydown.escape.window="close()"
        >
            <div class="grid gap-6 sm:grid-cols-2 lg:gap-8">
                @foreach ([['file' => 'genuine-device', 'label' => 'Phone Capture', 'result' => 'Detected as genuine capture', 'genuine' => true], ['file' => 'virtual-camera', 'label' => 'Virtual Camera', 'result' => 'Detected as simulated source', 'genuine' => false], ['file' => 'emulator', 'label' => 'Emulator', 'result' => 'Detected as simulated source', 'genuine' => false], ['file' => 'device-farm', 'label' => 'Device Farm', 'result' => 'Detected as simulated source', 'genuine' => false]] as $video)
                    <div
                        class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50"
                        @click="play({{ Js::from($video) }})"
                    >
                        <div class="relative aspect-video bg-neutral-900">
                            <img
                                src="/videos/{{ $video['file'] }}-poster.webp"
                                alt="{{ $video['label'] }} demo"
                                class="h-full w-full object-contain"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 transition-colors group-hover:bg-black/30">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 shadow-lg transition-transform group-hover:scale-110">
                                    <svg class="ml-0.5 h-6 w-6 text-neutral-900" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-3">
                            <span class="{{ $video['genuine'] ? 'bg-green-500' : 'bg-red-500' }} h-2 w-2 shrink-0 rounded-full"></span>
                            <p class="font-heading text-sm font-medium text-neutral-700">
                                {{ $video['label'] }} &mdash;
                                <span class="{{ $video['genuine'] ? 'text-green-600' : 'text-red-600' }}">{{ $video['result'] }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Lightbox Modal --}}
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-transition.opacity.duration.200ms
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    @click.self="close()"
                >
                    <div class="absolute inset-0 bg-black/80"></div>
                    <div class="relative z-10 w-full max-w-3xl" @click.stop>
                        {{-- Close button --}}
                        <button
                            @click="close()"
                            aria-label="Close video"
                            class="absolute -top-10 right-0 cursor-pointer text-white/70 transition-colors hover:text-white"
                            type="button"
                        >
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        {{-- Video --}}
                        <template x-if="activeVideo">
                            <div class="overflow-hidden rounded-xl bg-black">
                                <video
                                    x-ref="lightboxVideo"
                                    class="h-auto max-h-[80vh] w-full"
                                    controls
                                    playsinline
                                    muted
                                    preload="none"
                                    :poster="'/videos/' + activeVideo + '-poster.webp'"
                                >
                                    <source :src="'/videos/' + activeVideo + '.webm'" type="video/webm" />
                                    <source :src="'/videos/' + activeVideo + '.mp4'" type="video/mp4" />
                                </video>
                                <div class="flex items-center gap-2 bg-neutral-900 px-4 py-3">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-full"
                                        :class="activeGenuine ? 'bg-green-500' : 'bg-red-500'"
                                    ></span>
                                    <p class="font-heading text-sm font-medium text-neutral-200">
                                        <span x-text="activeLabel"></span> &mdash;
                                        <span
                                            :class="activeGenuine ? 'text-green-400' : 'text-red-400'"
                                            x-text="activeResult"
                                        ></span>
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </x-section>

    {{-- Section 8: Industry Solutions (Tabbed) --}}
    @php
        $industries = [
            [
                'id' => 'loan-draw',
                'label' => 'Loan Draw Inspections',
                'icon' => 'building-library',
                'headline' => 'Cut Site Visit Costs with Verified Photo Evidence',
                'citation' => null,
                'subheading' => 'Construction Verification Without Site Visits',
                'body' => 'Replace physical site visits with verified progress photos. Lenders can confirm images were captured on-site with authenticated GPS and timestamps — not uploaded from a gallery or recycled from a previous draw.',
                'bullets' => [
                    'Verified progress photos with authenticated timestamps and GPS',
                    'Live capture enforced — gallery uploads blocked at SDK level',
                    'Detection of recycled or manipulated construction photos',
                    'Auditable documentation for loan file compliance',
                ],
                'image' => 'images/mockups/loan-draw-construction.webp?v=2',
                'imageAlt' => 'Construction site progress photo',
            ],
            [
                'id' => 'insurance',
                'label' => 'Insurance Claims',
                'icon' => 'shield-exclamation',
                'headline' => 'Deepfake fraud attempts up 2,137% in three years',
                'citation' => [
                    'source' => 'Signicat',
                    'title' => 'The Battle Against AI-Driven Identity Fraud',
                    'year' => '2024',
                ],
                'subheading' => 'Accelerate Legitimate Claims. Flag Suspicious Submissions.',
                'body' => 'Automatically verify claim photos were taken at the reported location and time. Detect AI-generated damage documentation and altered evidence before it reaches an adjuster.',
                'bullets' => [
                    'Instant verification of claim photo authenticity',
                    'AI-generated and altered damage documentation flagged',
                    'Geolocation confirmation matching reported incident location',
                    'Reduced SIU referral backlog through automated flagging',
                ],
                'image' => 'images/mockups/insurance-car-damage.webp?v=2',
                'imageAlt' => 'Insurance claim damage photo',
            ],
            [
                'id' => 'asset',
                'label' => 'Field Operations & Assets',
                'icon' => 'cube',
                'headline' => 'Verify what exists, where it exists',
                'citation' => null,
                'subheading' => 'Authenticated Evidence for Field Operations & Assets',
                'body' => 'From proof of delivery to car rental damage inspection to warehouse inventory checks — confirm field documentation with timestamped, geolocated photography that can\'t be recycled or manipulated.',
                'bullets' => [
                    'Timestamped, geolocated asset photography',
                    'Photo manipulation and staging detection',
                ],
                'image' => 'images/mockups/asset-warehouse.webp?v=2',
                'imageAlt' => 'Asset warehouse inventory verification',
            ],
            [
                'id' => 'property',
                'label' => 'Property Inspections',
                'icon' => 'home',
                'headline' => 'Streamlining Remote Property Inspections',
                'citation' => null,
                'subheading' => 'Protect Your Rental Investment and Prevent Deposit Fraud',
                'body' => 'Transition from time-consuming manual site visits to secure, remote condition verification. PixaProof empowers property owners to verify the exact state of their rentals from the comfort of home. Ensure tenants leave the property clean and undamaged, eliminating the risk of renters submitting old or AI-altered photos to falsely claim their full security deposit.',
                'bullets' => [
                    'Capture property and furniture condition using mathematically authenticated timestamps and unalterable GPS coordinates.',
                    'Require in-the-moment photography to completely block the use of older images, recycled camera-roll shots, or AI-edited deepfakes.',
                    'Return tenant deposits with complete peace of mind, knowing your physical property and assets are genuinely intact.',
                ],
                'image' => 'images/mockups/property-inspection.webp',
                'imageAlt' => 'Remote rental property inspection with authenticated live capture',
            ],
        ];
    @endphp

    <x-section
        id="solutions"
        class="bg-neutral-50"
        eyebrow="Applications"
        title="Built for the Industries That Need It Most"
    >
        {{-- Tabbed Interface --}}
        <div x-data="{ activeTab: '{{ $industries[0]['id'] }}' }">
            {{-- Tab Navigation - Desktop with underline indicator --}}
            <div class="mb-8 hidden gap-3 md:flex">
                @foreach ($industries as $industry)
                    <button
                        @click="activeTab = '{{ $industry['id'] }}'"
                        :class="activeTab === '{{ $industry['id'] }}' ?
                            'border-primary-500 bg-primary-50 text-primary-700 border-b-2 border-b-primary-600' :
                            'border-neutral-200 bg-white text-neutral-600 hover:border-neutral-300 hover:bg-neutral-50'"
                        class="font-heading flex items-center gap-2 rounded-lg border px-5 py-3 text-sm font-semibold transition-colors"
                        type="button"
                    >
                        <x-dynamic-component :component="'heroicon-o-'.$industry['icon']" class="h-4 w-4" />
                        {{ $industry['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Mobile: Accordion --}}
            <div class="space-y-3 md:hidden">
                @foreach ($industries as $industry)
                    <button
                        @click="activeTab = activeTab === '{{ $industry['id'] }}' ? '' : '{{ $industry['id'] }}'"
                        :class="activeTab === '{{ $industry['id'] }}' ? 'border-primary-500 bg-primary-50' : 'border-neutral-200'"
                        class="flex w-full items-center justify-between rounded-lg border px-4 py-3 text-left font-semibold text-neutral-900 transition-colors"
                        type="button"
                    >
                        <span class="flex items-center gap-2">
                            <x-dynamic-component
                                :component="'heroicon-o-'.$industry['icon']"
                                class="text-primary-600 h-4 w-4"
                            />
                            {{ $industry['label'] }}
                        </span>
                        <x-heroicon-o-chevron-down
                            class="h-5 w-5 transition-transform"
                            x-bind:class="activeTab === '{{ $industry['id'] }}' ? 'rotate-180' : ''"
                        />
                    </button>
                    <div x-show="activeTab === '{{ $industry['id'] }}'" x-collapse class="px-4 pb-4">
                        <div class="text-primary-600 mb-2 text-2xl font-bold">{{ $industry['headline'] }}</div>
                        <p class="mb-4 text-neutral-700">{{ $industry['body'] }}</p>
                        <x-check-list :items="$industry['bullets']" compact />
                    </div>
                @endforeach
            </div>

            {{-- Tab Content - Desktop --}}
            <div class="hidden md:block">
                @foreach ($industries as $industry)
                    <div
                        x-show="activeTab === '{{ $industry['id'] }}'"
                        x-transition
                        class="grid items-start gap-8 rounded-lg border border-neutral-200 bg-white p-8 lg:grid-cols-5"
                    >
                        <div class="lg:col-span-3">
                            <div class="font-heading text-primary-600 mb-3 text-2xl font-bold">
                                {{ $industry['headline'] }}
                            </div>
                            @if ($industry['citation'])
                                <p class="mb-1 text-xs text-neutral-500">
                                    — {{ $industry['citation']['source'] }},
                                    <em>{{ $industry['citation']['title'] }}</em>, {{ $industry['citation']['year'] }}
                                </p>
                            @endif
                            <h3 class="mb-4 text-xl font-semibold text-neutral-900">{{ $industry['subheading'] }}</h3>
                            <p class="mb-6 text-neutral-700">{{ $industry['body'] }}</p>
                            <x-check-list :items="$industry['bullets']" />
                        </div>
                        <div class="flex justify-center lg:col-span-2">
                            <x-graphics.phone-mockup
                                variant="default"
                                size="lg"
                                :image="$industry['image']"
                                :imageAlt="$industry['imageAlt']"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- And more --}}
        <p class="mt-8 text-center text-sm text-neutral-500">
            Also used for: marketplace item verification, vehicle ownership, and site inspections.
        </p>
    </x-section>

    {{-- Section 8: Statistics Strip --}}
    <x-section class="bg-primary-600 noise-overlay relative" padding="py-16">
        <div class="grid grid-cols-2 gap-8 text-center lg:grid-cols-4">
            <x-stat value="10" suffix="M+" label="Verifications Processed" />
            <x-stat text="<500ms" label="Verification Speed" />
            <x-stat value="35" suffix="+" label="Integrity Checks" />
            <x-stat value="3" label="Granted Patents" />
        </div>
    </x-section>

    {{-- Section 9: Technology Highlights (Bento Grid) — COMMENTED OUT: data accuracy under review --}}
    @if (false)
        <section
            id="technology"
            class="bg-white py-20 lg:py-28"
            x-data="{ visible: false }"
            x-intersect.once="visible = true"
        >
            <div
                class="mx-auto max-w-7xl px-4 transition-all duration-700 ease-out"
                :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            >
                <div class="mb-12 text-center">
                    <p class="text-primary-600 font-heading mb-4 text-sm font-semibold tracking-wider uppercase">
                        Technology
                    </p>
                    <h2 class="font-heading mb-6 text-3xl font-bold text-neutral-900 md:text-4xl">
                        Enterprise-Grade Infrastructure You Can Trust
                    </h2>
                </div>

                {{-- Bento Grid --}}
                <div class="grid gap-6 md:grid-cols-3">
                    {{-- Large card: API Response Preview --}}
                    <div class="rounded-lg border border-neutral-200 bg-white p-8 transition-shadow hover:shadow-md md:col-span-2 md:row-span-2">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="bg-primary-100 flex h-12 w-12 items-center justify-center rounded-lg">
                                <x-heroicon-o-check-badge class="text-primary-600 h-7 w-7" />
                            </div>
                            <div>
                                <h3 class="font-heading text-xl font-bold text-neutral-900">
                                    Results in Under 500 Milliseconds
                                </h3>
                                <p class="flex items-center gap-1.5 text-sm text-neutral-500">
                                    <span class="relative flex h-2 w-2"
                                        ><span
                                            class="bg-primary-400 absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"
                                        ></span
                                        ><span class="bg-primary-500 relative inline-flex h-2 w-2 rounded-full"></span
                                    ></span>
                                    35+ checks, one API call
                                </p>
                            </div>
                        </div>
                        {{-- API Response mockup --}}
                        <div class="overflow-x-auto rounded-lg bg-neutral-900 p-5 font-mono text-sm leading-relaxed">
                            <div class="text-neutral-500">// POST /api/v1/verify</div>
                            <div class="mt-2 text-neutral-300">{</div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">authenticity_status</span>": "<span
                                    class="text-green-400"
                                    >verified</span
                                >",
                            </div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">capture_confidence</span>":
                                <span class="text-amber-400">0.98</span>,
                            </div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">live_capture</span>":
                                <span class="text-green-400">true</span>,
                            </div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">ai_generated</span>":
                                <span class="text-red-400">false</span>,
                            </div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">tampering_detected</span>":
                                <span class="text-red-400">false</span>,
                            </div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">gps_verified</span>":
                                <span class="text-green-400">true</span>,
                            </div>
                            <div class="ml-4 text-neutral-300">
                                "<span class="text-primary-400">processing_time_ms</span>":
                                <span class="text-amber-400">347</span>
                            </div>
                            <div class="text-neutral-300">}</div>
                        </div>
                    </div>

                    {{-- Medium card: Web SDK --}}
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 transition-shadow hover:shadow-md">
                        <div class="bg-primary-100 mb-4 flex h-12 w-12 items-center justify-center rounded-lg">
                            <x-heroicon-o-code-bracket class="text-primary-600 h-6 w-6" />
                        </div>
                        <h3 class="font-heading mb-2 text-lg font-semibold text-neutral-900">
                            Web SDK — Integrate in Hours
                        </h3>
                        <p class="text-sm text-neutral-600">
                            Browser-based SDK handles camera, location, and motion sensor access within your user flow.
                            The capture interface is customizable to match your brand identity.
                        </p>
                    </div>

                    {{-- Medium card: Real-Time Processing --}}
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 transition-shadow hover:shadow-md">
                        <div class="bg-primary-100 mb-4 flex h-12 w-12 items-center justify-center rounded-lg">
                            <x-heroicon-o-bolt class="text-primary-600 h-6 w-6" />
                        </div>
                        <h3 class="font-heading mb-2 text-lg font-semibold text-neutral-900">Real-Time, Not Batch</h3>
                        <p class="text-sm text-neutral-600">
                            Verification results returned via REST API before your user finishes the next step. No
                            queues, no delays.
                        </p>
                    </div>

                    {{-- Small card: On-Premise --}}
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 transition-shadow hover:shadow-md">
                        <div class="bg-primary-100 mb-3 flex h-10 w-10 items-center justify-center rounded-lg">
                            <x-heroicon-o-server class="text-primary-600 h-5 w-5" />
                        </div>
                        <h3 class="font-heading mb-1 text-base font-semibold text-neutral-900">On-Premise Available</h3>
                        <p class="text-sm text-neutral-600">
                            Deploy in your own infrastructure for data residency and air-gapped environments.
                        </p>
                    </div>

                    {{-- Small card: API-First --}}
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 transition-shadow hover:shadow-md">
                        <div class="bg-primary-100 mb-3 flex h-10 w-10 items-center justify-center rounded-lg">
                            <x-heroicon-o-command-line class="text-primary-600 h-5 w-5" />
                        </div>
                        <h3 class="font-heading mb-1 text-base font-semibold text-neutral-900">
                            API-First Architecture
                        </h3>
                        <p class="text-sm text-neutral-600">
                            During verification, our API checks hidden data sealed during capture to flag whether the
                            session was genuine or suspicious.
                        </p>
                    </div>

                    {{-- Small card: Roadmap --}}
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 transition-shadow hover:shadow-md">
                        <div class="bg-primary-100 mb-3 flex h-10 w-10 items-center justify-center rounded-lg">
                            <x-heroicon-o-map class="text-primary-600 h-5 w-5" />
                        </div>
                        <h3 class="font-heading mb-1 text-base font-semibold text-neutral-900">Roadmap</h3>
                        <p class="text-sm text-neutral-600">
                            Mobile SDKs for iOS & Android, deeper capture environment intelligence, and video support —
                            coming soon.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Section 10: Comparison Table --}}
    <x-section class="bg-neutral-50" width="max-w-5xl" eyebrow="Why PixaProof" title="How We Compare">
        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-lg border border-neutral-200 md:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-neutral-100">
                        <th class="font-heading px-6 py-4 text-left font-semibold text-neutral-900">Capability</th>
                        <th class="font-heading px-6 py-4 text-center font-semibold text-neutral-500">Manual Review</th>
                        <th class="font-heading text-primary-700 bg-primary-100 border-l-primary-500 border-l-2 px-6 py-4 text-center font-semibold">
                            PixaProof
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Detect gallery uploads</td>
                        <td class="px-6 py-4 text-center">
                            <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                        </td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Detect virtual cameras</td>
                        <td class="px-6 py-4 text-center">
                            <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                        </td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Detect emulator attacks</td>
                        <td class="px-6 py-4 text-center">
                            <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                        </td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Verify GPS authenticity</td>
                        <td class="px-6 py-4 text-center">
                            <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                        </td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Detect AI-generated content</td>
                        <td class="px-6 py-4 text-center">
                            <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                        </td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Detect pixel-level edits</td>
                        <td class="px-6 py-4 text-center text-neutral-500">Subjective</td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">End-to-end integrity (MITM)</td>
                        <td class="px-6 py-4 text-center">
                            <x-heroicon-o-x-mark class="mx-auto h-5 w-5 text-red-400" />
                        </td>
                        <td class="bg-primary-50 border-l-primary-500 border-l-2 px-6 py-4 text-center">
                            <x-heroicon-s-check class="mx-auto h-5 w-5 text-green-500" />
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-neutral-700">Time to verify</td>
                        <td class="px-6 py-4 text-center text-neutral-500">Hours–Days</td>
                        <td class="bg-primary-50 border-l-primary-500 text-primary-700 border-l-2 px-6 py-4 text-center font-semibold">
                            &lt;500ms
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Mobile: Stacked cards --}}
        <div
            class="space-y-4 md:hidden"
            x-intersect.once="$nextTick(() => Motion.staggerFadeIn($el.children, { stagger: 0.06, y: 15 }))"
        >
            @php
                $comparisons = [
                    ['label' => 'Detect gallery uploads', 'manual' => 'No', 'pixaproof' => 'Yes'],
                    ['label' => 'Detect virtual cameras', 'manual' => 'No', 'pixaproof' => 'Yes'],
                    ['label' => 'Detect emulator attacks', 'manual' => 'No', 'pixaproof' => 'Yes'],
                    ['label' => 'Verify GPS authenticity', 'manual' => 'No', 'pixaproof' => 'Yes'],
                    ['label' => 'Detect AI content', 'manual' => 'No', 'pixaproof' => 'Yes'],
                    ['label' => 'Detect pixel edits', 'manual' => 'Subjective', 'pixaproof' => 'Yes'],
                    ['label' => 'End-to-end integrity', 'manual' => 'No', 'pixaproof' => 'Yes'],
                    ['label' => 'Time to verify', 'manual' => 'Hours', 'pixaproof' => '<500ms'],
                ];
            @endphp
            @foreach ($comparisons as $row)
                <div class="rounded-lg border border-neutral-200 p-4">
                    <div class="mb-3 font-semibold text-neutral-900">{{ $row['label'] }}</div>
                    <div class="grid grid-cols-2 gap-2 text-center text-sm">
                        <div>
                            <div class="mb-1 text-xs text-neutral-500">Manual</div>
                            <div class="text-neutral-600">{{ $row['manual'] }}</div>
                        </div>
                        <div class="bg-primary-50 rounded p-1">
                            <div class="text-primary-600 mb-1 text-xs font-semibold">PixaProof</div>
                            <div class="text-primary-700 font-semibold">{{ $row['pixaproof'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- Section 11: Credibility / Heritage --}}
    <x-section
        id="about"
        class="bg-neutral-100"
        headerSpacing="mb-16"
        eyebrow="Proven Expertise"
        title="Built by Southeast Asia's Most Experienced Identity Verification Team"
        description="PixaProof is developed by Innov8tif, Southeast Asia's largest identity assurance provider. Over a decade of experience securing identity workflows for banks, insurers, and government agencies across ASEAN."
    >
        {{-- Timeline --}}
        <div class="relative mb-16">
            {{-- Connecting line (desktop) with gradient pulse --}}
            <div class="from-primary-300 via-primary-500 to-primary-300 absolute top-6 right-0 left-0 hidden h-0.5 animate-[shimmer_3s_ease-in-out_infinite] bg-gradient-to-r bg-[length:200%_100%] md:block"></div>

            <div class="grid grid-cols-2 gap-6 md:grid-cols-6">
                @php
                    $milestones = [
                        ['year' => '2011', 'label' => 'Founded as Innov8tif', 'icon' => 'flag'],
                        ['year' => '2019', 'label' => 'ISO 30107-3 Certified', 'icon' => 'shield-check'],
                        ['year' => '2020', 'label' => 'US Patent Granted', 'icon' => 'light-bulb'],
                        ['year' => '2023', 'label' => 'NexG / Bursa Listed', 'icon' => 'building-office'],
                        ['year' => '2024', 'label' => '10M+ Verifications', 'icon' => 'finger-print'],
                        ['year' => '2025', 'label' => 'PixaProof Launches', 'icon' => 'rocket-launch'],
                    ];
                @endphp
                @foreach ($milestones as $m)
                    <div class="relative text-center">
                        <div class="border-primary-500 relative z-10 mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full border-2 bg-white">
                            <x-dynamic-component
                                :component="'heroicon-o-'.$m['icon']"
                                class="text-primary-600 h-5 w-5"
                            />
                        </div>
                        <div class="font-heading text-lg font-bold text-neutral-900">{{ $m['year'] }}</div>
                        <div class="mt-1 text-sm text-neutral-600">{{ $m['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Certifications Row --}}
        <div class="grid gap-6 sm:grid-cols-3">
            <x-icon-card layout="stacked" iconSize="lg" icon="shield-check" title="ISO 30107-3">
                iBeta Level 1 & 2 Liveness Compliance
            </x-icon-card>
            <x-icon-card layout="stacked" iconSize="lg" icon="light-bulb" title="3 Patents">
                Including US patent for hologram detection
            </x-icon-card>
            <x-icon-card layout="stacked" iconSize="lg" icon="building-office" title="Bursa Malaysia Listed">
                Part of NexG Bhd. public-listed group
            </x-icon-card>
        </div>

        <div class="mt-10 text-center">
            <a
                href="https://innov8tif.com"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary-600 hover:text-primary-700 inline-flex items-center gap-2 transition-colors"
            >
                Learn more about our parent company
                <x-heroicon-o-arrow-right class="h-4 w-4" />
            </a>
        </div>
    </x-section>

    {{-- Section 12: FAQ --}}
    <x-section id="faq" class="bg-white" width="max-w-3xl" eyebrow="FAQ" title="Frequently Asked Questions">
        {{-- Accordion --}}
        <div x-data="{ openFaq: null }" class="space-y-3">
            @php
                $faqs = [
                    [
                        'q' => 'How does PixaProof integrate with existing systems?',
                        'a' => 'PixaProof provides a browser-based Web SDK and REST API. The Web SDK manages camera, location, and motion sensor permissions within your user flow. Integration involves embedding a JavaScript SDK and connecting verification results to your backend via API. Basic integration takes hours. Mobile SDKs for iOS and Android are on the roadmap.',
                    ],
                    [
                        'q' => 'How does PixaProof detect AI-generated images?',
                        'a' => 'Our verification engine runs 35+ checks including pixel-level analysis, metadata consistency verification, and purpose-built AI detection models trained on millions of synthetic and authentic images. The system identifies deepfakes, AI-generated documents, and digitally altered evidence with high confidence.',
                    ],
                    [
                        'q' => 'What is PIEA and how does it work?',
                        'a' => 'PIEA (Photo Integrity Encoding Algorithm) is our proprietary system that seals each capture with a tamper-evident digital watermark. At the moment of capture, the SDK binds the image to its environmental data — device, timestamp, GPS, and browser metadata. During verification, the server checks whether these sealed values remain consistent. This raises the bar significantly against tampering, making unauthorized alterations much harder to execute undetected.',
                    ],
                    [
                        'q' => 'Can PixaProof work with our existing mobile app?',
                        'a' => 'Currently, PixaProof offers a Web SDK that works in mobile browsers within your app\'s webview or standalone. Native mobile SDKs for iOS and Android are on our roadmap. Your users capture photos through the browser — PixaProof runs the verification automatically.',
                    ],
                    [
                        'q' => 'What deployment options are available?',
                        'a' => 'We offer a fully managed, cloud-hosted (SaaS) API. By handling the backend infrastructure on our end, we ensure you always have access to our fastest, most up-to-date verification capabilities with zero maintenance required on your side.',
                    ],
                    [
                        'q' => 'What happens when manipulation is detected?',
                        'a' => 'The API response indicates the verification as failed and provides supplementary information on geolocation.',
                    ],
                    [
                        'q' => 'What metadata does PixaProof collect?',
                        'a' => 'Browser-related metadata, location, timestamp, and device environment data — linked to the image at the moment of capture. All metadata is used exclusively for verification purposes.',
                    ],
                    [
                        'q' => 'What industries does PixaProof serve?',
                        'a' => 'PixaProof is built for any industry that relies on user-submitted photographic evidence: banking and lending (loan draw inspections), insurance (claims verification), delivery and field operations (proof of delivery, inspections), real estate, and e-commerce (product verification, returns).',
                    ],
                    [
                        'q' => 'How is data handled and protected?',
                        'a' => 'PixaProof supports international data protection standards including GDPR. Verification metadata retention is configurable per organizational policy. All data is encrypted in transit (TLS 1.3) and at rest.',
                    ],
                ];
            @endphp

            @foreach ($faqs as $index => $faq)
                <div
                    class="overflow-hidden rounded-lg border border-neutral-200 transition-colors"
                    :class="openFaq === {{ $index + 1 }} ? 'border-l-2 border-l-primary-500' : ''"
                >
                    <button
                        @click="openFaq = openFaq === {{ $index + 1 }} ? null : {{ $index + 1 }}"
                        class="font-heading flex w-full items-center justify-between gap-4 bg-white px-6 py-4 text-left font-semibold text-neutral-900 transition-colors hover:bg-neutral-50"
                        type="button"
                    >
                        {{ $faq['q'] }}
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
                        <p class="leading-relaxed text-neutral-700">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- Section 13: Final CTA --}}
    <x-section class="from-primary-50 to-primary-100 relative overflow-hidden bg-gradient-to-b" width="max-w-4xl">
        <x-slot:background>
            {{-- Radial dot pattern --}}
            <div class="absolute inset-0 bg-[radial-gradient(#0ea5e915_1.5px,transparent_1.5px)] [mask-image:radial-gradient(ellipse_at_center,black_30%,transparent_70%)] bg-[size:2rem_2rem]"></div>
        </x-slot:background>

        <div class="text-center">
            <h2 class="font-heading mb-6 text-3xl font-bold text-neutral-900 md:text-4xl">
                Every Unverified Image Is a Liability
            </h2>
            <p class="mx-auto mb-10 max-w-2xl text-xl text-neutral-600">
                Watch PixaProof verify live captures in under 500ms. Integrate our Web SDK in hours.
            </p>

            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <x-button href="/contact" size="lg"> Book a Demo </x-button>
                <x-button
                    href="mailto:sales@innov8tif.com"
                    variant="outline"
                    size="lg"
                    class="border-neutral-300 text-neutral-700 hover:bg-neutral-100"
                >
                    Email Sales Team
                </x-button>
            </div>

            <p class="mt-10 text-sm text-neutral-500">
                Trusted by the team behind 10M+ identity verifications across ASEAN
            </p>
        </div>
    </x-section>
@endsection
