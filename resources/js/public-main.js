// resources/js/public-main.js
import AOS from 'aos';
import 'aos/dist/aos.css'; // Assurez-vous que ce chemin est correct ou que app.css l'importe

(() => {
    'use strict';

    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // --- Éléments DOM ---
    const header = $('#header');
    const navMenu = $('#nav-menu'); // Menu pour mobile
    const navToggle = $('#nav-toggle'); // Bouton pour ouvrir menu mobile
    const navClose = $('#nav-close'); // Bouton pour fermer menu mobile
    const navLinks = $$('.nav__menu .nav__link'); // Liens dans le menu mobile/desktop pour fermer au clic
    const scrollUpBtn = $('#scroll-up');
    const sectionsWithId = $$('section[id]'); // Pour le navHighlighter

    // --- Initialisation AOS ---
    AOS.init({
        duration: 800,
        once: true,
        offset: 120, // Un peu plus pour que l'animation se déclenche un peu plus tard
        easing: 'ease-out-cubic',
        disable: window.innerWidth < 768 // Désactiver sur les petits écrans si la performance est un souci
    });

    // --- Menu Mobile (Interactions classiques si Alpine.js ne gère pas tout) ---
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.add('show-menu');
            document.body.classList.add('no-scroll'); // Empêche le scroll du body
        });
    }

    if (navClose && navMenu) {
        navClose.addEventListener('click', () => {
            navMenu.classList.remove('show-menu');
            document.body.classList.remove('no-scroll');
        });
    }

    // Fermer le menu mobile lors d'un clic sur un lien (pour navigation sur la même page ou pas)
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu && navMenu.classList.contains('show-menu')) {
                navMenu.classList.remove('show-menu');
                document.body.classList.remove('no-scroll');
            }
        });
    });
    // Si vous avez des dropdowns Alpine.js DANS le menu mobile,
    // assurez-vous que cliquer sur un lien de dropdown ferme aussi le menu mobile principal.

    // --- Gestion du Header au Scroll ---
    let lastScrollY = window.scrollY;
    const handleHeaderScroll = () => {
        if (!header) return;
        const currentScrollY = window.scrollY;
        if (currentScrollY >= 80) { // Seuil pour appliquer .scroll-header
            header.classList.add('scroll-header');
        } else {
            header.classList.remove('scroll-header');
        }
        // Optionnel: Cacher le header en scrollant vers le bas, le montrer en scrollant vers le haut
        // if (currentScrollY > lastScrollY && currentScrollY > header.offsetHeight) {
        //     header.classList.add('header-hidden'); // Vous devrez styler .header-hidden
        // } else {
        //     header.classList.remove('header-hidden');
        // }
        // lastScrollY = currentScrollY <= 0 ? 0 : currentScrollY;
    };

    // --- Gestion du Bouton Scroll Up ---
    const handleScrollUpButton = () => {
        if (!scrollUpBtn) return;
        if (window.scrollY >= 350) {
            scrollUpBtn.classList.add('show-scroll');
        } else {
            scrollUpBtn.classList.remove('show-scroll');
        }
    };

    if (scrollUpBtn) {
        scrollUpBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // --- Mise en évidence du lien actif dans la navigation au scroll ---
    const highlightActiveLink = () => {
        if (!header || sectionsWithId.length === 0) return;
        const scrollY = window.pageYOffset;
        // Hauteur du header fixe + un petit offset pour que le changement soit plus naturel
        const triggerOffset = (header.classList.contains('scroll-header') ? header.offsetHeight : (document.querySelector(':root')?.style.getPropertyValue('--header-height') ? parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--header-height')) * 16 : 80)) + 30;


        $$('.nav__menu .nav__link.active-link').forEach(active => active.classList.remove('active-link'));
        // Pour les dropdowns, il faudrait aussi potentiellement désactiver 'active-link' sur le bouton du dropdown

        let کوئی_سیکشن_فعال_نہیں = true; // Urdu: "koi section active nahin" -> no section is active

        sectionsWithId.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - triggerOffset;
            const sectionId = section.getAttribute('id');
            const link = $(`.nav__menu a[href*="#${sectionId}"]`); // Cible les liens d'ancrage

            if (link) {
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    link.classList.add('active-link');
                    کوئی_سیکشن_فعال_نہیں = false;

                    // Gérer le lien parent du dropdown si l'ancre est dans un dropdown
                    const dropdownParentButton = link.closest('.nav__item--dropdown')?.querySelector('.nav__link--button');
                    if(dropdownParentButton) {
                        dropdownParentButton.classList.add('active-link');
                    }

                } else {
                    link.classList.remove('active-link');
                }
            }
        });

        // Si aucune section n'est active par scroll (ex: on est tout en haut, ou sur une page sans ancres correspondantes)
        // alors on se fie au lien actif par la route (géré par Blade)
        // Sauf si le premier lien est "Accueil" et qu'on est tout en haut.
        if (کوئی_سیکشن_فعال_نہیں && window.scrollY < sectionsWithId[0]?.offsetTop - triggerOffset) {
            const homeLink = $(`.nav__menu a[href$="${route('public.home')}"]`); // Assurez-vous que `route('public.home')` renvoie le bon href
            if (homeLink && (window.location.pathname === '/' || window.location.href === route('public.home'))) { // ou une meilleure vérification de la page d'accueil
                 homeLink.classList.add('active-link');
            }
        }
    };


    // --- Mise à jour de l'année dans le Footer (géré par Blade, mais peut être un fallback) ---
    const updateFooterYear = () => {
        const yearSpan = $('#currentYear');
        if (yearSpan && yearSpan.textContent.includes('{{') && yearSpan.textContent.includes('}}')) { // Si c'est un placeholder Blade non rendu
            yearSpan.textContent = new Date().getFullYear().toString();
        } else if (yearSpan && yearSpan.textContent === "") { // Si vide
             yearSpan.textContent = new Date().getFullYear().toString();
        }
        // Si Blade fait déjà {{ date('Y') }}, cette fonction est moins critique.
    };

    // --- Écouteurs d'événements ---
    window.addEventListener('scroll', () => {
        requestAnimationFrame(() => {
            handleHeaderScroll();
            handleScrollUpButton();
            highlightActiveLink();
        });
    }, { passive: true }); // passive: true pour le scroll

    document.addEventListener('DOMContentLoaded', () => {
        handleHeaderScroll();
        handleScrollUpButton();
        highlightActiveLink();
        updateFooterYear();
        console.log("CRPQA Public JS Initialisé et prêt !");
    });

})();