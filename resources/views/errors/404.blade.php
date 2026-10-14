@extends('layout')

@section('seo')
    <s:mt:head title="Page not found" :canonical="false" status="404" />
@endsection

@section('content')
    <section class="py-32">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-primary-600 text-sm font-semibold tracking-wider uppercase">
                {{ $siteContent?->get('not_found_eyebrow') }}
            </p>
            <h1 class="mt-4 text-4xl font-bold text-neutral-900">{{ $siteContent?->get('not_found_heading') }}</h1>
            <p class="mt-4 text-lg text-neutral-600">{{ $siteContent?->get('not_found_text') }}</p>
            @if (filled($siteContent?->get('not_found_button_label')))
                <div class="mt-8">
                    <x-button :href="$siteContent->get('not_found_button_url')">{{ $siteContent->get('not_found_button_label') }}</x-button>
                </div>
            @endif
        </div>
    </section>
@endsection
