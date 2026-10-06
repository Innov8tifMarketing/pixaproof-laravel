@extends('layout')

@section('content')
    {{-- Hero Section --}}
    <section class="relative isolate overflow-hidden px-4 pt-32 pb-20 lg:pt-40 lg:pb-28">
        <div class="absolute inset-0 -z-10">
            <div class="from-primary-50 to-primary-50/50 absolute inset-0 bg-gradient-to-br via-white"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#0ea5e910_1px,transparent_1px),linear-gradient(to_bottom,#0ea5e910_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-3xl text-center">
            <div class="border-primary-500/40 bg-primary-500/10 mb-6 inline-flex items-center gap-2 rounded-full border px-4 py-2">
                <svg class="text-primary-600 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <span class="text-primary-600 text-sm font-medium">Contact Sales</span>
            </div>
            <h1 class="mb-4 text-4xl font-bold text-neutral-900 md:text-5xl">Talk to Our Team</h1>
            <p class="mx-auto mb-10 max-w-xl text-lg text-neutral-600">
                Book a demo, discuss your use case, or explore integration options with our solutions team.
            </p>

            {{-- Email CTA --}}
            <a
                href="mailto:sales@innov8tif.com"
                class="bg-accent-500 hover:bg-accent-600 inline-flex items-center gap-3 rounded-lg px-8 py-4 text-lg font-semibold text-white shadow-lg transition hover:shadow-xl"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                Email sales@innov8tif.com
            </a>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-neutral-500">
                <div class="flex items-center gap-2">
                    <svg class="text-primary-600 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Mon–Fri, 9 AM – 5 PM (GMT+8)</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="text-primary-600 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>We respond within 3 working days</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Office Locations --}}
    <section class="bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-5xl px-4">
            <div class="mb-12 text-center">
                <h2 class="font-heading mb-4 text-2xl font-bold text-neutral-900 md:text-3xl">Our Offices</h2>
                <p class="text-neutral-600">
                    Innov8tif Solutions serves 500+ clients across 10+ countries from our regional offices.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Malaysia (HQ) --}}
                <div class="rounded-lg border border-neutral-200 p-6">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-lg">🇲🇾</span>
                        <h3 class="font-heading font-semibold text-neutral-900">Malaysia (HQ)</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-neutral-600">
                        Innov8tif Solutions Sdn Bhd<br />
                        L9-2, Wisma Conlay,<br />
                        1, Jalan USJ 10/1,<br />
                        47620 Subang Jaya, Selangor
                    </p>
                </div>

                {{-- Singapore --}}
                <div class="rounded-lg border border-neutral-200 p-6">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-lg">🇸🇬</span>
                        <h3 class="font-heading font-semibold text-neutral-900">Singapore</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-neutral-600">
                        Innov8tif Solutions Pte Ltd<br />
                        120 Robinson Road, #12-01,<br />
                        Singapore 068913
                    </p>
                </div>

                {{-- Indonesia --}}
                <div class="rounded-lg border border-neutral-200 p-6">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-lg">🇮🇩</span>
                        <h3 class="font-heading font-semibold text-neutral-900">Indonesia</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-neutral-600">
                        PT. Innov8tif Karta Solusi<br />
                        Xin Building, Jl. Kapten Tendean No.52,<br />
                        Bandung City, West Java 40141
                    </p>
                </div>

                {{-- Cambodia --}}
                <div class="rounded-lg border border-neutral-200 p-6">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-lg">🇰🇭</span>
                        <h3 class="font-heading font-semibold text-neutral-900">Cambodia</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-neutral-600">
                        Innov8tif Solutions Co., Ltd.<br />
                        No. 206D, Street Preah Norodom,<br />
                        Sangkat Tonle Bassac, Phnom Penh
                    </p>
                </div>

                {{-- Philippines --}}
                <div class="rounded-lg border border-neutral-200 p-6">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-lg">🇵🇭</span>
                        <h3 class="font-heading font-semibold text-neutral-900">Philippines</h3>
                    </div>
                    <p class="text-sm leading-relaxed text-neutral-600">
                        7F, Finman Centre Building,<br />
                        131 Tordesillas, Salcedo Village,<br />
                        Makati City
                    </p>
                </div>
            </div>

            <p class="mt-10 text-center text-sm text-neutral-500">
                Built by the team behind 10M+ identity verifications across ASEAN — with ISO 27001:2022 certification
                and 3 granted patents.
            </p>
        </div>
    </section>
@endsection
