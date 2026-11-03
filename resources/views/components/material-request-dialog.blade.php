{{--
Material Request Dialog

The form in front of a downloadable material's file (Statamic's
`material_download` form). One per page, pushed onto the layout's `dialogs`
stack by <x-material-actions>; any button dispatching `request-material` with
{ id, title, url } opens it. Once the form is sent, the browser fetches the file
from the download route (App\Http\Controllers\MaterialDownloadController).
--}}

<div
    x-data="materialRequest"
    @request-material.window="show($event.detail)"
    @keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[60] flex items-end justify-center p-4 sm:items-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="material-request-title"
>
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-neutral-950/70 backdrop-blur-sm"
        @click="open = false"
    ></div>

    <div x-show="open" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
        <button
            type="button"
            class="absolute top-4 right-4 rounded-full p-1.5 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700"
            @click="open = false"
        >
            <span class="sr-only">Close</span>
            <x-heroicon-o-x-mark class="h-5 w-5" />
        </button>

        <div x-show="sent" class="py-6 text-center" role="status">
            <div class="bg-primary-50 mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full">
                <x-heroicon-o-arrow-down-tray class="text-primary-600 h-7 w-7" />
            </div>
            <h2 class="font-heading mb-2 text-xl font-bold text-neutral-900">Your download is starting</h2>
            <p class="text-neutral-600">We've also emailed you a link to it.</p>
        </div>

        <div x-show="! sent">
            <p class="text-primary-600 mb-2 text-sm font-semibold tracking-wider uppercase">Free download</p>
            <h2
                id="material-request-title"
                class="font-heading mb-2 pr-6 text-2xl font-bold text-neutral-900"
                x-text="material.title"
            ></h2>
            <p class="mb-6 text-sm text-neutral-600">
                Tell us who you are and the PDF downloads straight away. We'll email you a copy too.
            </p>

            <form
                action="{{ route('statamic.forms.submit', 'material_download') }}"
                method="POST"
                class="space-y-4"
                @submit.prevent="submit($el)"
                novalidate
            >
                @csrf
                <input type="hidden" name="material" :value="material.id" />
                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
                </div>

                <x-form-field
                    id="material-name"
                    name="name"
                    label="Name"
                    autocomplete="name"
                    required
                    x-ref="firstField"
                />
                <x-form-field
                    id="material-email"
                    name="email"
                    label="Work email"
                    type="email"
                    autocomplete="email"
                    required
                />
                <x-form-field
                    id="material-company"
                    name="company"
                    label="Company"
                    autocomplete="organization"
                    required
                />

                <p
                    x-show="errors.form || errors.material"
                    x-text="errors.form ?? errors.material"
                    class="text-sm text-red-600"
                    role="alert"
                ></p>

                <x-button
                    type="submit"
                    class="w-full disabled:cursor-wait disabled:opacity-70"
                    x-bind:disabled="sending"
                >
                    <span x-show="! sending">Download the PDF</span>
                    <span x-show="sending" x-cloak>Sending…</span>
                </x-button>

                <p class="text-center text-xs text-neutral-500">
                    We may contact you about PixaProof. See our
                    <a href="{{ url('/privacy') }}" class="hover:text-primary-700 underline">privacy policy</a>.
                </p>
            </form>
        </div>
    </div>
</div>
