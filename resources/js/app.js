import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

/**
 * BFCache (Back-Forward Cache) State Synchronization:
 * If the page is restored from browser in-memory cache after navigation,
 * ensure authentication state and dynamic data are refreshed.
 */
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});

/**
 * PT. Zein Internasional — Interaction Engine
 * ============================================
 * Uses Intersection Observer for scroll-reveal, vanilla JS for interactions.
 *
 * Animation data attributes:
 *   data-reveal               (single element reveal)
 *   data-reveal-stagger="100" (parent: ms between children)
 *   data-reveal-child         (children inside stagger parent or standalone)
 */

document.addEventListener('DOMContentLoaded', () => {
    const prefersReducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    /* =============================================
       SCROLL REVEAL — Intersection Observer (Deferred)
       ============================================= */
    const initScrollReveal = () => {
        if (!prefersReducedMotion && 'IntersectionObserver' in window) {
            // Staggered parent observer
            const staggerObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            const children = entry.target.querySelectorAll('[data-reveal-child]');
                            const staggerDelay = parseInt(entry.target.dataset.revealStagger) || 80;
                            children.forEach((child, i) => {
                                setTimeout(() => {
                                    child.classList.add('revealed');
                                }, i * staggerDelay);
                            });
                            staggerObserver.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.05, rootMargin: '50px 0px -20px 0px' }
            );

            document
                .querySelectorAll('[data-reveal-stagger]')
                .forEach((el) => staggerObserver.observe(el));

            // Single element observer
            const revealObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            const delay = parseInt(entry.target.dataset.revealDelay) || 0;
                            setTimeout(() => {
                                entry.target.classList.add('revealed');
                            }, delay);
                            revealObserver.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.05, rootMargin: '50px 0px -20px 0px' }
            );

            document
                .querySelectorAll('[data-reveal]')
                .forEach((el) => {
                    if (!el.closest('[data-reveal-stagger]')) {
                        revealObserver.observe(el);
                    }
                });

            // Safety fallback: reveal any remaining elements during idle time so content is NEVER stuck invisible
            const runFallback = () => {
                document
                    .querySelectorAll('[data-reveal]:not(.revealed), [data-reveal-child]:not(.revealed)')
                    .forEach((el) => el.classList.add('revealed'));
            };
            if ('requestIdleCallback' in window) {
                requestIdleCallback(runFallback, { timeout: 3000 });
            } else {
                setTimeout(runFallback, 3000);
            }
        } else {
            // Reduced motion or no IntersectionObserver: show everything immediately
            document
                .querySelectorAll('[data-reveal], [data-reveal-child]')
                .forEach((el) => el.classList.add('revealed'));
        }
    };

    if ('requestIdleCallback' in window) {
        requestIdleCallback(initScrollReveal, { timeout: 400 });
    } else {
        setTimeout(initScrollReveal, 60);
    }

    /* =============================================
       NAVBAR & SCROLL-TO-TOP (rAF Throttled)
       ============================================= */
    const navbar = document.getElementById('navbar');
    const scrollTopBtn = document.getElementById('scroll-top');
    let isScrolled = false;
    let ticking = false;

    const onScroll = () => {
        const scrollY = window.scrollY;
        if (navbar) {
            const shouldBeScrolled = scrollY > 30;
            if (shouldBeScrolled !== isScrolled) {
                isScrolled = shouldBeScrolled;
                navbar.classList.toggle('shadow-md', isScrolled);
            }
        }
        if (scrollTopBtn) {
            const show = scrollY > 400;
            scrollTopBtn.classList.toggle('opacity-0', !show);
            scrollTopBtn.classList.toggle('pointer-events-none', !show);
            scrollTopBtn.classList.toggle('translate-y-2', !show);
        }
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(onScroll);
            ticking = true;
        }
    }, { passive: true });

    onScroll();

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
            });
        });
    }

    /* =============================================
       SMOOTH SCROLL — Event Delegation
       ============================================= */
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href^="#"]');
        if (!link) return;
        const href = link.getAttribute('href');
        if (href === '#' || href === '#hero') {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
            window.dispatchEvent(new CustomEvent('close-mobile-menu'));
            return;
        }
        const id = href.substring(1);
        if (!id) return;
        const target = document.getElementById(id);
        if (target) {
            e.preventDefault();
            const navHeight = 70;
            const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 10;
            window.scrollTo({
                top,
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
            });
            window.dispatchEvent(new CustomEvent('close-mobile-menu'));
        }
    });

    /* =============================================
       LIGHTBOX
       ============================================= */
    window.openLightbox = (src, caption) => {
        const overlay = document.createElement('div');
        overlay.className =
            'fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-2 sm:p-4 md:p-6 lightbox-overlay cursor-pointer backdrop-blur-md';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', caption || 'Pratinjau Gambar');
        overlay.innerHTML = `
            <button class="absolute top-3 right-3 md:top-6 md:right-6 text-white/80 hover:text-white text-3xl md:text-4xl leading-none cursor-pointer transition-colors z-20" aria-label="Tutup">&times;</button>
            <div class="max-w-[95vw] max-h-[92vh] flex flex-col items-center justify-center" onclick="event.stopPropagation()">
                <div class="rounded-2xl overflow-hidden shadow-2xl bg-black/50 border border-white/10 flex items-center justify-center p-1">
                    <img src="${src}" alt="${caption || ''}" class="max-w-[95vw] max-h-[90vh] w-auto h-auto object-contain rounded-xl mx-auto">
                </div>
                ${caption ? `<p class="text-white/90 text-center mt-3 text-xs sm:text-sm font-medium drop-shadow">${caption}</p>` : ''}
            </div>
        `;

        // Close on backdrop click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.remove();
        });

        // Close button
        overlay
            .querySelector('button')
            .addEventListener('click', () => overlay.remove());

        // Close on Escape
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                overlay.remove();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        const cleanup = () => {
            document.body.style.overflow = '';
        };
        const mo = new MutationObserver(() => {
            if (!document.body.contains(overlay)) {
                cleanup();
                mo.disconnect();
            }
        });
        mo.observe(document.body, { childList: true });

        document.body.appendChild(overlay);
    };

    /* =============================================
       VIDEO MODAL (YOUTUBE EMBED LIGHTBOX)
       ============================================= */
    window.openVideoModal = (embedUrl, caption) => {
        const overlay = document.createElement('div');
        overlay.className =
            'fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-3 sm:p-6 md:p-8 cursor-pointer backdrop-blur-md';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', caption || 'Pemutar Video');
        
        // Append autoplay parameter if not present
        const urlSeparator = embedUrl.includes('?') ? '&' : '?';
        const finalUrl = embedUrl.includes('autoplay=') ? embedUrl : `${embedUrl}${urlSeparator}autoplay=1`;

        overlay.innerHTML = `
            <button class="absolute top-3 right-3 md:top-6 md:right-6 text-white/80 hover:text-white text-3xl md:text-4xl leading-none cursor-pointer transition-colors z-20" aria-label="Tutup">&times;</button>
            <div class="w-full max-w-4xl flex flex-col items-center justify-center" onclick="event.stopPropagation()">
                <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/15">
                    <iframe src="${finalUrl}" title="${caption || 'Video Dokumentasi'}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
                ${caption ? `<p class="text-white/90 text-center mt-3 text-xs sm:text-sm font-medium drop-shadow">${caption}</p>` : ''}
            </div>
        `;

        // Close on backdrop click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.remove();
        });

        // Close button
        overlay
            .querySelector('button')
            .addEventListener('click', () => overlay.remove());

        // Close on Escape
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                overlay.remove();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        const cleanup = () => {
            document.body.style.overflow = '';
        };
        const mo = new MutationObserver(() => {
            if (!document.body.contains(overlay)) {
                cleanup();
                mo.disconnect();
            }
        });
        mo.observe(document.body, { childList: true });

        document.body.appendChild(overlay);
    };

    /* =============================================
       UNIVERSAL PARALLAX ENGINE
       Supports Desktop, Tablets (iPad, Surface, Nest Hub) & Mobile
       ============================================= */
    const initParallax = () => {
        const sections = document.querySelectorAll('.fixed-bg-section');
        if (!sections.length) return;

        // Desktop with mouse/pointer and screen >= 1025px uses native CSS background-attachment: fixed
        const isDesktopFixed = () => {
            return (
                window.innerWidth >= 1025 &&
                window.matchMedia &&
                window.matchMedia('(hover: hover) and (pointer: fine)').matches
            );
        };

        let ticking = false;

        const updateParallax = () => {
            // If desktop is using native CSS fixed background, let CSS handle it with 0 JS overhead
            if (isDesktopFixed()) {
                sections.forEach((section) => {
                    const img = section.querySelector('.parallax-bg-img, .mobile-parallax-img');
                    if (img && img.style.transform) {
                        img.style.transform = '';
                    }
                });
                return;
            }

            // On touch devices, tablets (iPad, Surface, Nest Hub) and mobile:
            // smoothly lock the background image to viewport coordinates
            const viewportHeight = window.innerHeight;

            sections.forEach((section) => {
                const rect = section.getBoundingClientRect();

                // Only calculate if section is in or near viewport bounds
                if (rect.bottom >= -50 && rect.top <= viewportHeight + 50) {
                    const img = section.querySelector('.parallax-bg-img, .mobile-parallax-img');
                    if (img) {
                        const translateY = -rect.top;
                        img.style.transform = `translate3d(0, ${translateY}px, 0)`;
                    }
                }
            });

            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });

        window.addEventListener('resize', () => {
            updateParallax();
        }, { passive: true });

        // Initial alignment
        updateParallax();
    };

    initParallax();
});

