import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

// Motion animation library (motion.dev)
import { animate, scroll, spring } from 'motion';

// Expose Motion functions globally for Alpine.js integration
window.Motion = {
    animate,
    scroll,
    spring,

    // Reusable text swap animation with spring physics
    async textSwap(element, { onMidpoint }) {
        // Exit animation: fade out + slide up
        await animate(
            element,
            { opacity: 0, y: -20 },
            { duration: 0.25 }
        ).finished;

        // Execute callback at midpoint (e.g., change text)
        if (onMidpoint) onMidpoint();

        // Enter animation: fade in + slide down with spring
        await animate(
            element,
            { opacity: 1, y: 0 },
            { type: spring, stiffness: 400, damping: 25 }
        ).finished;
    },

    // Fade in with optional spring
    fadeIn(element, options = {}) {
        const { delay = 0, useSpring = false } = options;
        return animate(
            element,
            { opacity: [0, 1], y: [20, 0] },
            useSpring
                ? { type: spring, stiffness: 300, damping: 20, delay }
                : { duration: 0.4, delay }
        );
    },

    // Stagger fade-in for groups of elements
    staggerFadeIn(elements, options = {}) {
        const { stagger = 0.08, delay = 0, y = 20 } = options;
        const els = elements instanceof NodeList ? [...elements] : elements;
        els.forEach((el, i) => {
            el.style.opacity = '0';
            animate(
                el,
                { opacity: [0, 1], y: [y, 0] },
                { duration: 0.4, delay: delay + (i * stagger) }
            );
        });
    },

    // Animated counter with spring overshoot
    animateCounter(element, target, suffix = '') {
        const duration = 1200;
        const overshoot = 1.08;
        const start = performance.now();
        const tick = (now) => {
            const elapsed = now - start;
            let progress = Math.min(elapsed / duration, 1);
            // Ease-out with slight spring overshoot
            if (progress < 0.8) {
                progress = progress / 0.8;
                progress = 1 - Math.pow(1 - progress, 3);
                progress *= overshoot;
            } else {
                const remaining = (progress - 0.8) / 0.2;
                progress = overshoot - (overshoot - 1) * remaining;
            }
            const current = Math.round(target * Math.min(progress, 1));
            element.textContent = current.toLocaleString() + suffix;
            if (elapsed < duration) {
                requestAnimationFrame(tick);
            } else {
                element.textContent = target.toLocaleString() + suffix;
            }
        };
        requestAnimationFrame(tick);
    },

    // ── Declarative phase timeline ──
    // Adjust weights to change how long each phase holds during scroll.
    // Ranges are computed automatically — no manual recalculation needed.
    _scrubPhases: [
        { id: 'title',   weight: 1,   fade: 0.03 },
        { id: 'stat-0',  weight: 1.5, fade: 0.03 },
        { id: 'stat-1',  weight: 1.5, fade: 0.03 },
        { id: 'stat-2',  weight: 1.5, fade: 0.03 },
        { id: 'insight', weight: 1.2, fade: 0.04 },
        { id: 'gallery', weight: 2,   fade: 0.05 },
    ],

    // Compute start/end ranges from phase weights (cached on first call)
    _computePhaseRanges() {
        if (this._phaseRanges) return this._phaseRanges;
        const totalWeight = this._scrubPhases.reduce((sum, p) => sum + p.weight, 0);
        let cursor = 0;
        this._phaseRanges = this._scrubPhases.map(phase => {
            const span = phase.weight / totalWeight;
            const range = { id: phase.id, start: cursor, end: cursor + span, fade: phase.fade };
            cursor += span;
            return range;
        });
        return this._phaseRanges;
    },

    // Background color stops — transition aligned with phase gaps for WCAG AA.
    // White holds through title + stat-0; transition happens during the stat-0→stat-1
    // crossfade (when content opacity ≈ 0) so the mid-tone zone is invisible.
    _bgColorStops: [
        { at: 0.25, color: 'rgb(255, 255, 255)' },       // white through stat-0
        { at: 0.30, color: 'rgb(3, 105, 161)' },         // primary-700 at phase gap
        { at: 0.55, color: 'rgb(15, 23, 42)' },          // neutral-900 for later phases
    ],

    // Text color stops — switches dark→light during the same phase gap.
    // Dark text on white (stat-0), light text on primary-700+ (stat-1 onwards).
    _textColorStops: [
        { at: 0.25, color: 'rgb(15, 23, 42)' },          // neutral-900 (dark)
        { at: 0.30, color: 'rgb(241, 245, 249)' },       // neutral-100 (light)
    ],

    // Interpolate between color stops given a progress value
    _interpolateColorStops(stops, progress) {
        if (progress <= stops[0].at) return stops[0].color;
        if (progress >= stops[stops.length - 1].at) return stops[stops.length - 1].color;

        for (let i = 0; i < stops.length - 1; i++) {
            if (progress >= stops[i].at && progress <= stops[i + 1].at) {
                const t = (progress - stops[i].at) / (stops[i + 1].at - stops[i].at);
                return this._lerpRgb(stops[i].color, stops[i + 1].color, t);
            }
        }
        return stops[stops.length - 1].color;
    },

    // Linear interpolation between two rgb() color strings
    _lerpRgb(colorA, colorB, t) {
        const parse = (c) => c.match(/\d+/g).map(Number);
        const [r1, g1, b1] = parse(colorA);
        const [r2, g2, b2] = parse(colorB);
        const r = Math.round(r1 + (r2 - r1) * t);
        const g = Math.round(g1 + (g2 - g1) * t);
        const b = Math.round(b1 + (b2 - b1) * t);
        return `rgb(${r},${g},${b})`;
    },

    // Active scroll controller reference (for teardown on breakpoint change)
    _scrubCleanup: null,

    // Scroll-scrubable challenge section orchestration
    initScrubSection() {
        const section = document.querySelector('[data-scrub-section]');
        if (!section) return;

        const mql = window.matchMedia('(max-width: 767px)');

        const init = () => {
            // Teardown previous instance if any
            if (this._scrubCleanup) {
                this._scrubCleanup();
                this._scrubCleanup = null;
            }

            if (mql.matches) {
                this._scrubCleanup = this._initScrubMobile(section);
            } else {
                this._scrubCleanup = this._initScrubDesktop(section);
            }
        };

        // Reinitialize on breakpoint change (handles resize/orientation)
        mql.addEventListener('change', init);
        init();
    },

    // Desktop: scroll-scrub animation with motion's scroll()
    _initScrubDesktop(section) {
        const bg = section.querySelector('[data-scrub-bg]');
        const viewport = section.querySelector('[data-scrub-viewport]');
        const titleEl = section.querySelector('[data-scrub-phase="title"]');
        const stats = section.querySelectorAll('[data-scrub-stat]');
        const insightEl = section.querySelector('[data-scrub-phase="insight"]');
        const galleryEl = section.querySelector('[data-scrub-phase="gallery"]');
        const cards = section.querySelectorAll('[data-scrub-card]');

        const remap = (p, start, end) => Math.max(0, Math.min(1, (p - start) / (end - start)));
        const ranges = this._computePhaseRanges();

        // Set initial states
        [titleEl, insightEl, galleryEl, ...stats].forEach(el => {
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
            }
        });
        cards.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
        });

        // Stat data
        const statTargets = [90, 67, 30];
        const statSuffixes = ['%', '%', '%'];

        // Track last-written bg/text colors and counter values to skip redundant DOM writes
        let lastBgColor = '';
        let lastTextColor = '';

        const scrollControl = scroll(
            (progress) => {
                // ── Background color (interpolated from stops) ──
                if (bg) {
                    const bgColor = this._interpolateColorStops(this._bgColorStops, progress);
                    if (bgColor !== lastBgColor) {
                        bg.style.backgroundColor = bgColor;
                        lastBgColor = bgColor;
                    }
                }

                // ── Text color (interpolated from stops) ──
                if (viewport) {
                    const textColor = this._interpolateColorStops(this._textColorStops, progress);
                    if (textColor !== lastTextColor) {
                        viewport.style.color = textColor;
                        lastTextColor = textColor;
                    }
                }

                // ── Phase: Title ──
                const titleRange = ranges[0];
                if (titleEl) {
                    const fadeIn = remap(progress, titleRange.start, titleRange.start + titleRange.fade);
                    const fadeOut = 1 - remap(progress, titleRange.end - titleRange.fade, titleRange.end);
                    titleEl.style.opacity = Math.min(fadeIn, fadeOut);
                    titleEl.style.transform = `translateY(${(1 - fadeIn) * 30}px)`;
                }

                // ── Phase: Stats (ranges[1], ranges[2], ranges[3]) ──
                stats.forEach((stat, i) => {
                    const range = ranges[i + 1]; // stat-0 = ranges[1], etc.
                    const fadeIn = remap(progress, range.start, range.start + range.fade);
                    const fadeOut = 1 - remap(progress, range.end - range.fade, range.end);
                    stat.style.opacity = Math.min(fadeIn, fadeOut);
                    stat.style.transform = `translateY(${(1 - fadeIn) * 30}px)`;

                    // Counter: only update DOM when value changes
                    const counter = stat.querySelector('[data-scrub-counter]');
                    if (counter) {
                        const countEnd = range.start + (range.end - range.start) * 0.4;
                        const countProgress = remap(progress, range.start, countEnd);
                        const value = Math.round(statTargets[i] * countProgress);
                        const newText = value + statSuffixes[i];
                        if (counter._lastText !== newText) {
                            counter.textContent = newText;
                            counter._lastText = newText;
                        }
                    }
                });

                // ── Phase: Insight ──
                const insightRange = ranges[4];
                if (insightEl) {
                    const fadeIn = remap(progress, insightRange.start, insightRange.start + insightRange.fade);
                    const fadeOut = 1 - remap(progress, insightRange.end - insightRange.fade, insightRange.end);
                    const scale = 0.92 + 0.08 * fadeIn;
                    insightEl.style.opacity = Math.min(fadeIn, fadeOut);
                    insightEl.style.transform = `translateY(${(1 - fadeIn) * 20}px) scale(${scale})`;
                }

                // ── Phase: Gallery (holds until section ends) ──
                const galleryRange = ranges[5];
                if (galleryEl) {
                    const fadeIn = remap(progress, galleryRange.start, galleryRange.start + galleryRange.fade);
                    galleryEl.style.opacity = fadeIn;
                    galleryEl.style.transform = `translateY(${(1 - fadeIn) * 30}px)`;
                }

                // Stagger cards within gallery
                cards.forEach((card, i) => {
                    const cardStart = galleryRange.start + 0.02 + i * 0.025;
                    const cardFade = remap(progress, cardStart, cardStart + 0.05);
                    card.style.opacity = cardFade;
                    card.style.transform = `translateY(${(1 - cardFade) * 20}px)`;
                });
            },
            {
                target: section,
                offset: ['start start', 'end end'],
            }
        );

        // Return cleanup function for breakpoint teardown
        return () => {
            if (scrollControl && scrollControl.stop) scrollControl.stop();
            // Reset inline styles so mobile layout renders cleanly
            [titleEl, insightEl, galleryEl, ...stats, ...cards].forEach(el => {
                if (el) {
                    el.style.opacity = '';
                    el.style.transform = '';
                }
            });
            if (bg) bg.style.backgroundColor = '';
            if (viewport) viewport.style.color = '';
        };
    },

    // Mobile fallback: single IntersectionObserver with data-attribute routing
    _initScrubMobile(section) {
        const allElements = section.querySelectorAll('[data-scrub-phase], [data-scrub-stat], [data-scrub-card]');
        const cards = section.querySelectorAll('[data-scrub-card]');
        let cardsTriggered = false;

        // Hide all elements initially
        allElements.forEach(el => { el.style.opacity = '0'; });

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const el = entry.target;

                    if (el.dataset.scrubPhase !== undefined) {
                        // Phase element: simple fade-in
                        Motion.fadeIn(el, { delay: 0.1 });
                        observer.unobserve(el);
                    } else if (el.dataset.scrubStat !== undefined) {
                        // Stat element: fade-in + animate counter
                        Motion.fadeIn(el, { delay: 0.1 });
                        const counter = el.querySelector('[data-scrub-counter]');
                        if (counter) {
                            const target = parseInt(counter.dataset.target);
                            const suffix = counter.dataset.suffix || '';
                            Motion.animateCounter(counter, target, suffix);
                        }
                        observer.unobserve(el);
                    } else if (el.dataset.scrubCard !== undefined && !cardsTriggered) {
                        // First card triggers stagger for all cards
                        cardsTriggered = true;
                        Motion.staggerFadeIn(cards, { stagger: 0.08, delay: 0.1 });
                        cards.forEach(card => observer.unobserve(card));
                    }
                });
            },
            { threshold: 0.2 }
        );

        allElements.forEach(el => observer.observe(el));

        // Return cleanup function for breakpoint teardown
        return () => {
            observer.disconnect();
            allElements.forEach(el => {
                el.style.opacity = '';
                el.style.transform = '';
            });
        };
    }
};

// Initialize scroll-scrubable sections
document.addEventListener('DOMContentLoaded', () => {
    Motion.initScrubSection();
});

// Alpine.js with the plugins the templates use (x-collapse, x-intersect).
// Started last, so Alpine expressions can use window.Motion.
Alpine.plugin(collapse);
Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();

