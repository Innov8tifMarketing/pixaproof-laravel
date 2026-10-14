{{-- A page: its sections, from the page builder (components/blocks). --}}
@extends('layout')

@section('content')
    <x-blocks :blocks="$page->blocks" :page="$page" />
@endsection
