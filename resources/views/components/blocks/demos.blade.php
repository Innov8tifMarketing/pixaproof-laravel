{{-- Demo videos: cards that open the video in a lightbox (teleported to <body>). --}}
@php
    $videos = collect($block->videos ?? [])->map(fn ($video) => [
        'label' => (string) $video->label,
        'result' => (string) $video->result,
        'genuine' => (bool) $video->genuine,
        'poster' => $video->poster?->url(),
        'webm' => $video->webm?->url(),
        'mp4' => $video->mp4?->url(),
    ]);
@endphp
<x-section
    :id="$block->anchor"
    class="bg-white"
    descriptionWidth="max-w-2xl"
    :eyebrow="$block->eyebrow"
    :title="$block->heading"
    :description="$block->intro"
>
    <div
        x-data="{
            open: false,
            activeVideo: null,
            activeLabel: '',
            activeResult: '',
            activeGenuine: false,
            play(video) {
                this.activeVideo = video;
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
            @foreach ($videos as $video)
                <div
                    class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50"
                    @click="play({{ Js::from($video) }})"
                >
                    <div class="relative aspect-video bg-neutral-900">
                        @if ($video['poster'])
                            <img
                                src="{{ $video['poster'] }}"
                                alt="{{ $video['label'] }} demo"
                                class="h-full w-full object-contain"
                                loading="lazy"
                            />
                        @endif
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
                                :poster="activeVideo.poster"
                            >
                                <source :src="activeVideo.webm" type="video/webm" />
                                <source :src="activeVideo.mp4" type="video/mp4" />
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
