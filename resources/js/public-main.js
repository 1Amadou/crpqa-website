import AOS from 'aos';
import 'aos/dist/aos.css';

(() => {
    'use strict';

    const $ = selector => document.querySelector(selector);
    const $$ = selector => document.querySelectorAll(selector);

    const header = $('#header');
    const navMenu = $('#nav-menu');
    const navToggle = $('#nav-toggle');
    const navClose = $('#nav-close');
    const navLinks = $$('.nav__menu .nav__link');
    const scrollUpBtn = $('#scroll-up');
    const sectionsWithId = $$('section[id]');
    const currentYearSpan = $('#currentYear');

    AOS.init({
        duration: 800,
        offset: 120,
        once: true,
        easing: 'ease-out-cubic',
        disable: window.innerWidth < 768
    });

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.add('show-menu');
            document.body.classList.add('no-scroll');
        });
    }

    if (navClose && navMenu) {
        navClose.addEventListener('click', () => {
            navMenu.classList.remove('show-menu');
            document.body.classList.remove('no-scroll');
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu && navMenu.classList.contains('show-menu')) {
                if (!link.closest('.nav__item--dropdown') || !link.classList.contains('nav__link--button')) {
                    navMenu.classList.remove('show-menu');
                    document.body.classList.remove('no-scroll');
                }
            }
        });
    });

    const handleHeaderScroll = () => {
        if (!header) return;
        if (window.scrollY >= 50) {
            header.classList.add('scroll-header');
        } else {
            header.classList.remove('scroll-header');
        }
    };

    const handleScrollUpButton = () => {
        if (!scrollUpBtn) return;
        if (window.scrollY >= 350) {
            scrollUpBtn.classList.add('show-scroll');
        } else {
            scrollUpBtn.classList.remove('show-scroll');
        }
    };

    if (scrollUpBtn) {
        scrollUpBtn.addEventListener('click', e => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const highlightActiveLink = () => {
        if (!header || sectionsWithId.length === 0) return;

        const scrollY = window.pageYOffset;
        const rootStyles = getComputedStyle(document.documentElement);
        const headerHeightValue = rootStyles.getPropertyValue('--header-height').trim();
        let headerOffset = 80;
        if (headerHeightValue.includes('rem')) {
            headerOffset = parseFloat(headerHeightValue) * parseFloat(getComputedStyle(document.documentElement).fontSize);
        } else if (headerHeightValue.includes('px')) {
            headerOffset = parseFloat(headerHeightValue);
        }
        headerOffset += 30;

        $$('.nav__menu .nav__link.active-link').forEach(active => active.classList.remove('active-link'));
        $$('.nav__menu .nav__link--button.active-link').forEach(active => active.classList.remove('active-link'));

        let isAnySectionActive = false;

        sectionsWithId.forEach(currentSection => {
            const sectionHeight = currentSection.offsetHeight;
            const sectionTop = currentSection.offsetTop - headerOffset;
            const sectionId = currentSection.getAttribute('id');
            const correspondingLink = $(`.nav__menu a[href*="#${sectionId}"]`);

            if (correspondingLink) {
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    correspondingLink.classList.add('active-link');
                    isAnySectionActive = true;
                    const dropdownButton = correspondingLink.closest('.nav__item--dropdown')?.querySelector('.nav__link--button');
                    if (dropdownButton) {
                        dropdownButton.classList.add('active-link');
                    }
                } else {
                    correspondingLink.classList.remove('active-link');
                }
            }
        });

        if (!isAnySectionActive && window.scrollY < (sectionsWithId[0]?.offsetTop - headerOffset || 300)) {
            const homeLink = $(`.nav__menu a[href$="${route('public.home')}"]`);
            if (homeLink && (window.location.pathname === '/' || window.location.pathname === route('public.home'))) {
                // Active link for home is managed by server-side rendering
            }
        }
    };

    const updateFooterYear = () => {
        if (currentYearSpan) {
            if (currentYearSpan.textContent.trim() === "" || currentYearSpan.textContent.includes('{{')) {
                currentYearSpan.textContent = new Date().getFullYear().toString();
            }
        }
    };

    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        scrollTimeout = window.requestAnimationFrame(() => {
            handleHeaderScroll();
            handleScrollUpButton();
            highlightActiveLink();
        });
    }, { passive: true });

    document.addEventListener('DOMContentLoaded', () => {
        handleHeaderScroll();
        handleScrollUpButton();
        highlightActiveLink();
        updateFooterYear();
        console.info("CRPQA Public JS initialisé et prêt.");
    });

    document.addEventListener('DOMContentLoaded', () => {
        const partnersCarousel = document.querySelector('.partners-carousel');
        if (partnersCarousel && typeof Swiper !== 'undefined') {
            try {
                new Swiper(partnersCarousel, {
                    loop: true,
                    slidesPerView: 2,
                    spaceBetween: 20,
                    grabCursor: true,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: '.partners-carousel__pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.partners-carousel__nav-next',
                        prevEl: '.partners-carousel__nav-prev'
                    },
                    breakpoints: {
                        576: { slidesPerView: 3, spaceBetween: 25 },
                        768: { slidesPerView: 4, spaceBetween: 30 },
                        992: { slidesPerView: 5, spaceBetween: 35 },
                        1200: { slidesPerView: 6, spaceBetween: 40 }
                    }
                });
            } catch (e) {
                console.error('Error initializing Swiper for partners:', e);
            }
        } else if (partnersCarousel && typeof Swiper === 'undefined') {
            console.warn('Swiper library is not loaded, but .partners-carousel element exists.');
        }

        const publicationsContainer = document.getElementById('latest-publications-content');
        if (publicationsContainer) {
            const loadingSpinner = publicationsContainer.querySelector('.loading-spinner');
            const loadingMessage = publicationsContainer.querySelector('.loading-message');
            const publicationsRoute = "{{ route('api.latest-publications', [], false) }}";

            if (publicationsRoute && !publicationsRoute.includes('{{') && !publicationsRoute.includes('}}')) {
                fetch(publicationsRoute)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status + ' ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (loadingSpinner) loadingSpinner.style.display = 'none';
                        if (loadingMessage) loadingMessage.style.display = 'none';

                        if (data.publications && data.publications.length > 0) {
                            const ul = document.createElement('ul');
                            ul.classList.add('publications-list');

                            data.publications.forEach(pub => {
                                const li = document.createElement('li');
                                li.classList.add('publication-item');

                                const titleLink = document.createElement('a');
                                titleLink.href = pub.url || '#';
                                if (pub.url) {
                                    titleLink.target = '_blank';
                                    titleLink.rel = 'noopener noreferrer';
                                }
                                titleLink.textContent = pub.title || 'Titre non disponible';

                                const titleH5 = document.createElement('h5');
                                titleH5.appendChild(titleLink);

                                const metaP = document.createElement('p');
                                metaP.classList.add('publication-meta');
                                let metaText = [];
                                if (pub.authors) metaText.push(`Auteurs: ${pub.authors}`);
                                if (pub.year) metaText.push(`Année: ${pub.year}`);
                                if (pub.journal) metaText.push(`Journal: ${pub.journal}`);
                                metaP.textContent = metaText.join(' - ') || 'Informations non disponibles';

                                li.appendChild(titleH5);
                                li.appendChild(metaP);
                                ul.appendChild(li);
                            });
                            publicationsContainer.appendChild(ul);
                        } else {
                            publicationsContainer.innerHTML = '<p class="text-muted">Aucune publication récente à afficher.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching latest publications:', error);
                        if (loadingSpinner) loadingSpinner.style.display = 'none';
                        if (loadingMessage) loadingMessage.style.display = 'none';
                        publicationsContainer.innerHTML = '<p class="text-danger">Erreur lors du chargement des publications. Veuillez réessayer plus tard.</p>';
                    });
            } else {
                console.warn('Route for latest publications is not properly defined. AJAX call skipped.');
                if (loadingSpinner) loadingSpinner.style.display = 'none';
                if (loadingMessage) loadingMessage.style.display = 'none';
                publicationsContainer.innerHTML = '<p class="text-warning">La section des publications est en cours de configuration.</p>';
            }
        }
    });
})();
