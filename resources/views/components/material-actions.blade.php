{{--
Material Actions Component

A downloadable material's buttons: "Download" (opens the request dialog, which
this pushes onto the page once) and, when the material has a video, a link to it.

Usage:
<x-material-actions :material="$entry" />
<x-material-actions :material="$entry" label="Download the use case" />

Props:
- material: an entry from App\Support\DownloadableMaterials
- label: the download button's text (default: Download PDF)
--}}

@props([
    'material',
    'label' => 'Download PDF',
])

@pushOnce('dialogs')
    <x-material-request-dialog />
@endPushOnce

<div {{ $attributes->class('flex flex-wrap items-center gap-3') }}>
    <x-button
        size="sm"
        :icon="false"
        x-data
        @click="$dispatch('request-material', {{
            Js::from([
                'id' => (string) $material->id(),
                'title' => (string) $material->get('title'),
                'url' => route('materials.download', $material->slug()),
            ])
        }})"
    >
        <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
        {{ $label }}
    </x-button>

    @if (filled($material->get('video_url')))
        <x-button
            :href="$material->get('video_url')"
            variant="secondary"
            size="sm"
            :icon="false"
            target="_blank"
            rel="noopener noreferrer"
        >
            <x-heroicon-o-play-circle class="h-4 w-4" />
            Watch the video
        </x-button>
    @endif
</div>
