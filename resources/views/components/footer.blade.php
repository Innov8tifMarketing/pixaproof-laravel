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
                <p class="mt-4 text-sm text-neutral-600">
                    Enterprise-grade image authenticity verification. Detect tampering, validate metadata, and establish
                    chain of custody.
                </p>
                <p class="mt-4 text-sm text-neutral-500">
                    A product of
                    <a
                        href="https://innov8tif.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:text-primary-600 text-neutral-600 transition"
                    >Innov8tif</a>
                </p>
            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-sm font-semibold tracking-wider text-neutral-500 uppercase">Navigation</h3>
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
                <h3 class="text-sm font-semibold tracking-wider text-neutral-500 uppercase">Contact</h3>
                <ul class="mt-4 space-y-2">
                    <li>
                        <a href="/contact" class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >Request Demo</a>
                    </li>
                    <li>
                        <a
                            href="mailto:sales@innov8tif.com"
                            class="hover:text-primary-600 text-sm text-neutral-600 transition"
                        >sales@innov8tif.com</a>
                    </li>
                    <li>
                        <a
                            href="https://innov8tif.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:text-primary-600 text-sm text-neutral-600 transition"
                        >innov8tif.com</a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-sm font-semibold tracking-wider text-neutral-500 uppercase">Legal</h3>
                <ul class="mt-4 space-y-2">
                    <li>
                        <a href="/privacy" class="hover:text-primary-600 text-sm text-neutral-600 transition"
                            >Privacy Policy</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-neutral-200 pt-8 md:flex-row">
            <div class="text-sm text-neutral-500">
                &copy; {{ date('Y') }} Innov8tif Solutions Pte. Ltd. All rights reserved.
            </div>
            <div class="flex gap-6">
                <a href="/privacy" class="hover:text-primary-600 text-sm text-neutral-500 transition"> Privacy </a>
                <a href="/contact" class="hover:text-primary-600 text-sm text-neutral-500 transition"> Contact </a>
            </div>
        </div>
    </div>
</footer>
