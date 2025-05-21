// resources/js/public-main.js

// Importation d'AOS (si installé via npm)
import AOS from 'aos';
// Importation du CSS d'AOS (Vite s'en chargera)
import 'aos/dist/aos.css'; // S'assure que les styles AOS sont inclus dans votre bundle CSS

(() => {
    'use strict'; // Active le mode strict pour de meilleures pratiques de codage

    // Fonctions utilitaires pour sélectionner les éléments du DOM
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // --- Éléments DOM fréquemment utilisés ---
    const header = $('#header');
    const navMenu = $('#nav-menu');         // Le conteneur du menu mobile
    const navToggle = $('#nav-toggle');     // Le bouton "hamburger" pour ouvrir
    const navClose = $('#nav-close');       // Le bouton "croix" pour fermer
    const navLinks = $$('.nav__menu .nav__link'); // Tous les liens dans le nav__menu
    const scrollUpBtn = $('#scroll-up');
    const sectionsWithId = $$('section[id]'); // Sections ayant un ID (pour le navHighlighter)
    const currentYearSpan = $('#currentYear'); // Pour l'année dans le footer

    // --- Initialisation d'AOS (Animate On Scroll) ---
    AOS.init({
        duration: 800,          // Durée par défaut des animations
        offset: 120,            // Décalage (en px) par rapport au viewport pour déclencher l'animation
        once: true,             // Animation jouée une seule fois par élément
        easing: 'ease-out-cubic',// Courbe d'animation douce
        disable: window.innerWidth < 768 // Option: désactiver AOS sur les petits écrans (téléphones)
    });

    // --- Gestion du Menu Mobile ---
    // Logique pour ouvrir le menu mobile
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.add('show-menu'); // Classe définie dans votre style.css
            document.body.classList.add('no-scroll'); // Empêche le défilement du body
        });
    }

    // Logique pour fermer le menu mobile
    if (navClose && navMenu) {
        navClose.addEventListener('click', () => {
            navMenu.classList.remove('show-menu');
            document.body.classList.remove('no-scroll');
        });
    }

    // Fermer le menu mobile lorsqu'un lien à l'intérieur est cliqué
    // Utile pour les ancres ou la navigation vers une autre page
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu && navMenu.classList.contains('show-menu')) {
                // Si le lien n'est pas un bouton ouvrant un sous-menu Alpine
                if (!link.closest('.nav__item--dropdown') || !link.classList.contains('nav__link--button')) {
                    navMenu.classList.remove('show-menu');
                    document.body.classList.remove('no-scroll');
                }
                // Si c'est un bouton de dropdown Alpine, on laisse Alpine gérer son état 'open'
                // mais le menu principal doit quand même se fermer SI ce n'est pas un lien direct.
                // La logique Alpine est sur les 'button' dans les 'li.nav__item--dropdown'
            }
        });
    });

    // --- Changement d'apparence du Header au Scroll ---
    // Ajoute/retire la classe '.scroll-header' au header
    const handleHeaderScroll = () => {
        if (!header) return; // Vérifie si l'élément header existe
        if (window.scrollY >= 50) { // Seuil de scroll avant d'appliquer la classe
            header.classList.add('scroll-header');
        } else {
            header.classList.remove('scroll-header');
        }
    };

    // --- Affichage/Masquage du Bouton "Scroll Up" ---
    // Ajoute/retire la classe '.show-scroll'
    const handleScrollUpButton = () => {
        if (!scrollUpBtn) return; // Vérifie si le bouton existe
        if (window.scrollY >= 350) { // Seuil de scroll
            scrollUpBtn.classList.add('show-scroll');
        } else {
            scrollUpBtn.classList.remove('show-scroll');
        }
    };

    // Action de clic pour le bouton "Scroll Up"
    if (scrollUpBtn) {
        scrollUpBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // Défilement doux
            });
        });
    }

    // --- Mise en Évidence du Lien Actif dans la Navigation au Scroll (Nav Highlighter) ---
    const highlightActiveLink = () => {
        // S'assurer que header et sectionsWithId existent et qu'il y a des sections à analyser
        if (!header || sectionsWithId.length === 0) return;

        const scrollY = window.pageYOffset;
        // Calculer l'offset du header. Prend en compte la hauteur du header fixe.
        // La valeur de --header-height est récupérée depuis les styles CSS.
        const rootStyles = getComputedStyle(document.documentElement);
        const headerHeightValue = rootStyles.getPropertyValue('--header-height').trim();
        let headerOffset = 80; // Valeur par défaut si --header-height n'est pas bien définie ou en rem
        if (headerHeightValue.includes('rem')) {
            headerOffset = parseFloat(headerHeightValue) * parseFloat(getComputedStyle(document.documentElement).fontSize);
        } else if (headerHeightValue.includes('px')) {
            headerOffset = parseFloat(headerHeightValue);
        }
        headerOffset += 30; // Marge supplémentaire pour un déclenchement plus agréable

        // D'abord, retirer 'active-link' de tous les liens et boutons de dropdown
        $$('.nav__menu .nav__link.active-link').forEach(active => active.classList.remove('active-link'));
        $$('.nav__menu .nav__link--button.active-link').forEach(active => active.classList.remove('active-link'));


        let isAnySectionActive = false;

        sectionsWithId.forEach(currentSection => {
            const sectionHeight = currentSection.offsetHeight;
            const sectionTop = currentSection.offsetTop - headerOffset;
            const sectionId = currentSection.getAttribute('id');
            // Cible les liens d'ancrage qui pointent vers l'ID de la section
            const correspondingLink = $(`.nav__menu a[href*="#${sectionId}"]`);

            if (correspondingLink) {
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    correspondingLink.classList.add('active-link');
                    isAnySectionActive = true;

                    // Si le lien actif est dans un dropdown, activer aussi le bouton parent du dropdown
                    const dropdownButton = correspondingLink.closest('.nav__item--dropdown')?.querySelector('.nav__link--button');
                    if (dropdownButton) {
                        dropdownButton.classList.add('active-link');
                    }
                } else {
                    correspondingLink.classList.remove('active-link');
                }
            }
        });

        // Si après avoir parcouru toutes les sections, aucun lien d'ancre n'est actif
        // (par exemple, on est en haut de la page, ou sur une page sans ces ancres),
        // on se fie à la classe 'active-link' mise par Blade pour la route actuelle.
        // Cette partie est surtout pour les pages à section unique avec scrollspy.
        // Pour la page d'accueil, si on est tout en haut avant la première section,
        // on pourrait forcer le lien "Accueil" à être actif.
        if (!isAnySectionActive && window.scrollY < (sectionsWithId[0]?.offsetTop - headerOffset || 300)) {
            const homeLink = $(`.nav__menu a[href$="${route('public.home')}"]`); // Suppose une fonction `route()` globale ou un chemin direct
            if (homeLink && (window.location.pathname === '/' || window.location.pathname === route('public.home'))) {
                 // La classe active-link pour la route est déjà gérée par Blade, donc ici on s'assure juste
                 // que les autres ne sont pas mis en évidence par erreur.
                 // Si le lien 'Accueil' est le seul qui devrait être actif en haut de la page, Blade s'en charge déjà.
            }
        }
    };
    // Fonction `route()` n'est pas définie en JS par défaut. Vous devez soit passer les routes à JS,
    // soit utiliser des sélecteurs plus génériques pour le lien d'accueil si `route('public.home')` ne fonctionne pas ici.
    // Par exemple: const homeLink = $(`.nav__menu a[href="/"]`); ou basé sur un data-attribute.


    // --- Mise à jour de l'Année dans le Footer ---
    const updateFooterYear = () => {
        if (currentYearSpan) {
            // Si le span existe et que Blade n'a pas déjà mis l'année (par exemple, si son contenu est vide ou un placeholder)
            if (currentYearSpan.textContent.trim() === "" || currentYearSpan.textContent.includes('{{')) {
                currentYearSpan.textContent = new Date().getFullYear().toString();
            }
            // Sinon, on fait confiance à Blade qui a déjà mis {{ date('Y') }}
        }
    };

    // --- Écouteurs d'Événements Globaux ---
    // Utilisation de requestAnimationFrame pour optimiser les fonctions de scroll
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
    }, { passive: true }); // `passive: true` pour indiquer que l'écouteur ne bloquera pas le scroll

    // Exécution des fonctions au chargement initial du DOM
    document.addEventListener('DOMContentLoaded', () => {
        handleHeaderScroll();       // Appliquer le style initial du header
        handleScrollUpButton();     // Vérifier l'état initial du bouton scroll-up
        highlightActiveLink();      // Mettre en évidence le lien actif au chargement
        updateFooterYear();         // Mettre à jour l'année (si nécessaire)

        console.info("CRPQA Public JS initialisé et prêt.");
    });

})();