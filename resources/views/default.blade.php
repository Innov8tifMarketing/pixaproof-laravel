@extends('layout')

@section('content')
    <section class="py-20">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-neutral-900">{{ $title }}</h1>
            <p class="mt-4 text-sm text-neutral-500">Last updated: {{ $page->lastModified()->format('F j, Y') }}</p>

            <div class="prose prose-lg prose-neutral prose-h2:text-2xl prose-h2:font-semibold prose-h2:text-neutral-900 prose-p:text-neutral-700 prose-li:text-neutral-700 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:text-primary-500 mt-8">
                {!! $content !!}
            </div>
        </div>
    </section>
@endsection
