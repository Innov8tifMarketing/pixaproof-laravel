<nav
    x-data="{
        mobileMenuOpen: false,
        scrolled: false,
        init() {
            const notHome = window.location.pathname !== '/';
            this.scrolled = notHome || window.scrollY > 50;
            window.addEventListener('scroll', () => {
                this.scrolled = notHome || window.scrollY > 50;
            });
        },
    }"
    :class="scrolled
        ? 'bg-white/95 backdrop-blur-sm shadow-sm border-neutral-200'
        : 'bg-transparent border-transparent'"
    class="fixed top-0 right-0 left-0 z-50 border-b transition-all duration-300"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <img
                    src="{{ asset('images/pixaproof-wordmark.svg') }}"
                    alt="PixaProof"
                    width="1347"
                    height="232"
                    class="h-8 w-auto shrink-0"
                />
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden items-center gap-1 lg:flex">
                <s:nav:main>
                    <a
                        href="{{ $url }}"
                        class="hover:text-primary-600 rounded px-3 py-2 text-sm font-medium text-neutral-700 transition-colors duration-300 hover:bg-neutral-50"
                    >
                        {{ $title }}
                    </a>
                </s:nav:main>

                <!-- Request Demo CTA Button -->
                <div class="ml-4 flex items-center gap-3">
                    <a
                        href="/contact"
                        class="bg-accent-500 hover:bg-accent-600 inline-flex items-center gap-2 rounded px-4 py-2 text-sm font-semibold text-white transition"
                    >
                        Request Demo
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center gap-2 lg:hidden">
                <button
                    @click="mobileMenuOpen = ! mobileMenuOpen"
                    type="button"
                    aria-label="Toggle menu"
                    class="rounded p-2 text-neutral-700 transition-colors duration-300 hover:bg-neutral-100"
                >
                    <svg
                        x-show="! mobileMenuOpen"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg
                        x-show="mobileMenuOpen"
                        x-cloak
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="mobileMenuOpen" x-collapse x-cloak class="border-t border-neutral-200 py-4 lg:hidden">
            <div class="transition-colors duration-300">
                <s:nav:main>
                    <a
                        href="{{ $url }}"
                        @click="mobileMenuOpen = false"
                        class="hover:text-primary-600 block px-2 py-2 text-sm font-medium text-neutral-700 transition-colors duration-300"
                    >
                        {{ $title }}
                    </a>
                </s:nav:main>

                <div class="mt-4 border-t border-neutral-200 pt-4 transition-colors duration-300">
                    <a
                        href="/contact"
                        class="bg-accent-500 hover:bg-accent-600 inline-flex items-center gap-2 rounded px-4 py-2 text-sm font-semibold text-white transition"
                    >
                        Request Demo
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
