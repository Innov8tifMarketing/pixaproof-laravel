{{-- Contact form: the closing call to action. A heading, text, points and the
     direct email on the left; the enquiry form (Statamic's `contact` form, posted
     by Alpine) on the right. Submissions are mailed by App\Listeners\SendContactEnquiry. --}}
@php
    $bag = 'form.contact';
    $interests = \Statamic\Facades\Form::find('contact')?->blueprint()->field('interest')?->get('options') ?? [];
    $serverErrors = collect($errors->getBag($bag)->messages())->map(fn (array $messages) => $messages[0]);
    $points = collect($block->points ?? [])->filter()->values()->all();
@endphp

<section
    @if (filled($block->anchor)) id="{{ $block->anchor }}" @endif
    class="relative isolate overflow-hidden bg-neutral-950 py-20 lg:py-28"
>
    {{-- Glow and grid --}}
    <div class="absolute inset-0 -z-10" aria-hidden="true">
        <div class="bg-primary-600/30 absolute -top-40 -left-32 h-[32rem] w-[32rem] rounded-full blur-3xl"></div>
        <div class="bg-accent-500/15 absolute -right-24 -bottom-48 h-[28rem] w-[28rem] rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff0a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff0a_1px,transparent_1px)] [mask-image:radial-gradient(ellipse_at_center,black_40%,transparent_80%)] bg-[size:4rem_4rem]"></div>
    </div>

    <div class="mx-auto grid max-w-7xl items-start gap-12 px-4 lg:grid-cols-2 lg:gap-16">
        {{-- Pitch --}}
        <div class="lg:sticky lg:top-28">
            @if (filled($block->badge))
                <div class="border-primary-400/30 bg-primary-400/10 text-primary-300 mb-6 inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-medium">
                    <span class="bg-primary-400 h-1.5 w-1.5 rounded-full"></span>
                    {{ $block->badge }}
                </div>
            @endif

            <h2 class="font-heading mb-6 text-4xl font-bold tracking-tight text-white md:text-5xl">
                {{ $block->heading }}
            </h2>

            @if (filled($block->text))
                <p class="mb-8 max-w-xl text-lg text-neutral-300">{{ $block->text }}</p>
            @endif

            @if ($points !== [])
                <ul class="mb-10 space-y-4">
                    @foreach ($points as $point)
                        <li class="flex items-start gap-3 text-neutral-200">
                            <span class="bg-primary-500/20 mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full">
                                <x-heroicon-s-check class="text-primary-300 h-4 w-4" />
                            </span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if (filled($block->email) || filled($block->note))
                <div class="flex flex-col gap-3 border-t border-white/10 pt-8 text-sm text-neutral-400 sm:flex-row sm:items-center sm:gap-8">
                    @if (filled($block->email))
                        <a
                            href="mailto:{{ $block->email }}"
                            class="inline-flex items-center gap-2 transition-colors hover:text-white"
                        >
                            <x-heroicon-o-envelope class="h-5 w-5" />
                            {{ $block->email }}
                        </a>
                    @endif
                    @if (filled($block->note))
                        <span class="inline-flex items-center gap-2">
                            <x-heroicon-o-clock class="h-5 w-5" />
                            {{ $block->note }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Form --}}
        <div
            x-data="contactForm"
            x-init="errors = @js($serverErrors)"
            class="rounded-2xl bg-white p-6 shadow-2xl shadow-black/40 sm:p-8"
        >
            <div x-show="sent" x-cloak class="py-10 text-center" role="status">
                <div class="bg-primary-50 mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full">
                    <x-heroicon-o-check-circle class="text-primary-600 h-8 w-8" />
                </div>
                <h3 class="font-heading mb-2 text-2xl font-bold text-neutral-900">
                    {{ $block->success_heading ?: 'Thank you' }}
                </h3>
                <p class="mx-auto max-w-sm text-neutral-600">{{ $block->success_text }}</p>
            </div>

            <div x-show="! sent">
                @if (session("{$bag}.success"))
                    <div class="bg-primary-50 text-primary-800 mb-6 rounded-lg p-4 text-sm" role="status">
                        <strong>{{ $block->success_heading ?: 'Thank you' }}.</strong> {{ $block->success_text }}
                    </div>
                @endif

                @if (filled($block->form_heading))
                    <h3 class="font-heading mb-6 text-xl font-semibold text-neutral-900">{{ $block->form_heading }}</h3>
                @endif

                <form
                    action="{{ route('statamic.forms.submit', 'contact') }}"
                    method="POST"
                    class="grid gap-5 sm:grid-cols-2"
                    @submit.prevent="submit($el)"
                    novalidate
                >
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
                    </div>

                    <x-form-field name="name" label="Name" autocomplete="name" required :bag="$bag" />
                    <x-form-field
                        name="email"
                        label="Work email"
                        type="email"
                        autocomplete="email"
                        required
                        :bag="$bag"
                    />
                    <x-form-field name="company" label="Company" autocomplete="organization" required :bag="$bag" />
                    <x-form-field name="phone" label="Phone" type="tel" autocomplete="tel" :bag="$bag" />
                    <x-form-field
                        name="interest"
                        label="Interested in"
                        :options="$interests"
                        required
                        :bag="$bag"
                        class="sm:col-span-2"
                    />
                    <x-form-field
                        name="message"
                        label="Message"
                        textarea
                        required
                        placeholder="What do you need to verify, and in which workflow?"
                        :bag="$bag"
                        class="sm:col-span-2"
                    />

                    <div class="sm:col-span-2">
                        <p
                            x-show="errors.form"
                            x-text="errors.form"
                            x-cloak
                            class="mb-4 text-sm text-red-600"
                            role="alert"
                        ></p>

                        <x-button
                            type="submit"
                            size="lg"
                            class="w-full disabled:cursor-wait disabled:opacity-70"
                            x-bind:disabled="sending"
                        >
                            <span x-show="! sending">{{ $block->button_label ?: 'Send message' }}</span>
                            <span x-show="sending" x-cloak>Sending…</span>
                        </x-button>

                        <p class="mt-4 text-center text-xs text-neutral-500">
                            We use your details only to answer you. See our
                            <a href="{{ url('/privacy') }}" class="hover:text-primary-700 underline">privacy policy</a>.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
