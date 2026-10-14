<footer class="border-t border-neutral-200 bg-neutral-50">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Main Footer Grid -->
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Brand Column -->
            <div class="sm:col-span-2 lg:col-span-1">
                <img
                    src="{{ asset('images/pixaproof-wordmark.svg') }}"
                    alt="PixaProof"
                    width="1347"
                    height="232"
                    class="h-8 w-auto shrink-0"
                />
                @if (filled($siteContent?->get('footer_blurb')))
                    <p class="mt-4 text-sm text-neutral-600">{{ $siteContent->get('footer_blurb') }}</p>
                @endif
                @if (filled($siteContent?->get('parent_name')))
                    <p class="mt-4 text-sm text-neutral-500">
                        {{ $siteContent->get('parent_prefix') }}
                        <a
                            href="{{ $siteContent->get('parent_url') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:text-primary-600 text-neutral-600 transition"
                        >{{ $siteContent->get('parent_name') }}</a>
                    </p>
                @endif
            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    {{ $siteContent?->get('navigation_heading') }}
                </h3>
                <ul class="mt-4 space-y-2">
                    {{-- sheath-disable a11y-list-semantics -- the nav tag renders no element, so each <li> lands directly in the <ul> --}}
                    <s:nav:footer>
                        <li>
                            <a
                                href="{{ $url }}"
                                class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >{{ $title }}</a>
                        </li>
                    </s:nav:footer>
                    {{-- sheath-enable a11y-list-semantics --}}
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    {{ $siteContent?->get('contact_heading') }}
                </h3>
                <ul class="mt-4 space-y-2">
                    @if (filled($siteContent?->get('cta_label')))
                        <li>
                            <a
                                href="{{ $siteContent->get('cta_url') }}"
                                class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >{{ $siteContent->get('cta_label') }}</a>
                        </li>
                    @endif
                    @if (filled($siteContent?->get('contact_email')))
                        <li>
                            <a
                                href="mailto:{{ $siteContent->get('contact_email') }}"
                                class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >{{ $siteContent->get('contact_email') }}</a>
                        </li>
                    @endif
                    @if (filled($siteContent?->get('website_url')))
                        <li>
                            <a
                                href="{{ $siteContent->get('website_url') }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >{{ $siteContent->get('website_label') }}</a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-sm font-semibold tracking-wider text-neutral-500 uppercase">
                    {{ $siteContent?->get('legal_heading') }}
                </h3>
                <ul class="mt-4 space-y-2">
                    @foreach ($siteContent?->get('legal_links') ?? [] as $link)
                        <li>
                            <a
                                href="{{ $link['url'] ?? '' }}"
                                class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >{{ $link['label'] ?? '' }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-neutral-200 pt-8 md:flex-row">
            <div class="text-sm text-neutral-500">&copy; {{ date('Y') }} {{ $siteContent?->get('copyright') }}</div>
            <div class="flex gap-6">
                @foreach ($siteContent?->get('bottom_links') ?? [] as $link)
                    <a
                        href="{{ $link['url'] ?? '' }}"
                        class="hover:text-primary-600 text-sm text-neutral-500 transition"
                    >
                        {{ $link['label'] ?? '' }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
