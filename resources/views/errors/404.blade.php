@extends('layout')

@section('seo')
    <s:mt:head title="Page not found" :canonical="false" status="404" />
@endsection

@section('content')
    <section class="py-32">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-primary-600 text-sm font-semibold tracking-wider uppercase">404</p>
            <h1 class="mt-4 text-4xl font-bold text-neutral-900">Page not found</h1>
            <p class="mt-4 text-lg text-neutral-600">The page you're looking for doesn't exist or has moved.</p>
            <div class="mt-8">
                <x-button href="/">Back to home</x-button>
            </div>
        </div>
    </section>
@endsection
