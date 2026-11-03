{{--
Form Field Component

A labelled input, textarea or select for a Statamic form posted by Alpine
(resources/js/app.js). The error shows from the Alpine `errors` object; after a
post without JavaScript it is printed from the form's error bag.

Usage:
<x-form-field name="email" label="Work email" type="email" autocomplete="email" required bag="form.contact" />
<x-form-field name="interest" label="Interested in" :options="['demo' => 'A demo']" bag="form.contact" />
<x-form-field name="message" label="Message" textarea bag="form.contact" />

Props:
- name / label: the field handle and its label
- type: input type (default: text)
- textarea: a textarea instead of an input
- options: value => label; makes it a select
- bag: the error bag Statamic fills, `form.{handle}`
- required, autocomplete, placeholder: as on the control
--}}

@props([
    'name',
    'label',
    'type' => 'text',
    'textarea' => false,
    'options' => null,
    'bag' => 'default',
    'required' => false,
    'autocomplete' => null,
    'placeholder' => null,
])

@php
    $id = $attributes->get('id', "field-{$name}-".substr(md5($bag), 0, 6));
    $serverError = $errors->getBag($bag)->first($name);
    $control = 'block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition placeholder:text-neutral-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none';
@endphp

<div {{ $attributes->except(['id', 'x-ref'])->class('flex flex-col gap-1.5') }}>
    <label for="{{ $id }}" class="text-sm font-medium text-neutral-800">
        {{ $label }}
        @if ($required)
            <span class="text-accent-600" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($options !== null)
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            class="{{ $control }}"
            @required($required)
            :aria-invalid="errors.{{ $name }} ? 'true' : 'false'"
        >
            <option value="" disabled @selected(blank(old($name)))>Choose one</option>
            @foreach ($options as $value => $optionLabel)
                <option value="{{ $value }}" @selected(old($name) === (string) $value)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif ($textarea)
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="4"
            class="{{ $control }} resize-y"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @required($required)
            :aria-invalid="errors.{{ $name }} ? 'true' : 'false'"
        >{{ old($name) }}</textarea>
    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ old($name) }}"
            class="{{ $control }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @required($required)
            :aria-invalid="errors.{{ $name }} ? 'true' : 'false'"
            {{ $attributes->only('x-ref') }}
        />
    @endif

    <p
        class="text-sm text-red-600"
        role="alert"
        x-show="errors.{{ $name }}"
        x-text="errors.{{ $name }}"
        @unless ($serverError) x-cloak @endunless
    >
        {{ $serverError }}
    </p>
</div>
