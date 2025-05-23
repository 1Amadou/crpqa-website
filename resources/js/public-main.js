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

    document.addEventListener('DOMContentLoaded', function() {

    /**
     * ------------------------------------------------------------------------
     * Carrousel des Partenaires (SwiperJS)
     * ------------------------------------------------------------------------
     */
    const partnersCarousel = document.querySelector('.partners-carousel');
    if (partnersCarousel && typeof Swiper !== 'undefined') {
        // Vérifie que l'élément existe ET que la librairie Swiper est chargée
        try {
            new Swiper(partnersCarousel, {
                // Options de base (vous pouvez les personnaliser)
                loop: true,
                slidesPerView: 2, // Nombre de slides visibles sur les plus petits écrans
                spaceBetween: 20, // Espace entre les slides en pixels
                grabCursor: true,
                autoplay: {
                    delay: 4000, // Délai entre les transitions en ms
                    disableOnInteraction: false, // L'autoplay ne s'arrête pas après interaction manuelle
                },
                pagination: {
                    el: '.partners-carousel__pagination', // Sélecteur de votre conteneur de pagination
                    clickable: true,
                },
                navigation: {
                    nextEl: '.partners-carousel__nav-next', // Sélecteur de votre bouton "suivant"
                    prevEl: '.partners-carousel__nav-prev', // Sélecteur de votre bouton "précédent"
                },
                // Points de rupture pour la responsivité
                breakpoints: {
                    576: { // sm
                        slidesPerView: 3,
                        spaceBetween: 25
                    },
                    768: { // md
                        slidesPerView: 4,
                        spaceBetween: 30
                    },
                    992: { // lg
                        slidesPerView: 5,
                        spaceBetween: 35
                    },
                    1200: { // xl
                        slidesPerView: 6, // Ajustez en fonction de la largeur de vos logos
                        spaceBetween: 40
                    }
                }
            });
            // console.log('Swiper for partners initialized.');
        } catch (e) {
            console.error('Error initializing Swiper for partners:', e);
        }
    } else if (partnersCarousel && typeof Swiper === 'undefined') {
        console.warn('Swiper library is not loaded, but .partners-carousel element exists.');
    }


    /**
     * ------------------------------------------------------------------------
     * Chargement AJAX des Dernières Publications
     * ------------------------------------------------------------------------
     */
    const publicationsContainer = document.getElementById('latest-publications-content');
    if (publicationsContainer) {
        const loadingSpinner = publicationsContainer.querySelector('.loading-spinner');
        const loadingMessage = publicationsContainer.querySelector('.loading-message');
        const publicationsRoute = "{{ route('api.latest-publications', [], false) }}"; // Génère l'URL sans le domaine

        // Vérifier si la route est un placeholder ou une vraie URL
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

                            // Créer le titre avec lien
                            const titleLink = document.createElement('a');
                            titleLink.href = pub.url || '#'; // Lien de la publication
                            if (pub.url) { // N'ouvrir dans un nouvel onglet que si l'URL existe
                                titleLink.target = '_blank';
                                titleLink.rel = 'noopener noreferrer';
                            }
                            titleLink.textContent = pub.title || 'Titre non disponible';

                            const titleH5 = document.createElement('h5');
                            titleH5.appendChild(titleLink);

                            // Créer les métadonnées
                            const metaP = document.createElement('p');
                            metaP.classList.add('publication-meta');
                            let metaText = [];
                            if(pub.authors) metaText.push(`Auteurs: ${pub.authors}`);
                            if(pub.year) metaText.push(`Année: ${pub.year}`);
                            if(pub.journal) metaText.push(`Journal: ${pub.journal}`); // Si vous avez cette info
                            metaP.textContent = metaText.join(' - ') || 'Informations non disponibles';

                            li.appendChild(titleH5);
                            li.appendChild(metaP);
                            ul.appendChild(li);
                        });
                        publicationsContainer.appendChild(ul);
                        // console.log('Latest publications loaded.');
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

    /**
     * ------------------------------------------------------------------------
     * Timeline Historique (Animations gérées par AOS)
     * ------------------------------------------------------------------------
     */
    // Pour la timeline, les animations sont principalement gérées par les attributs `data-aos`
    // dans le HTML et la bibliothèque AOS elle-même, qui devrait être initialisée globalement.
    // Si vous souhaitez des interactions JavaScript spécifiques pour la timeline
    // (ex: ouvrir/fermer des détails au clic), vous les ajouteriez ici.
    // Actuellement, aucune action JS spécifique n'est requise pour la timeline
    // basée sur la structure et les styles CSS fournis.

    // Vous pouvez vous assurer que AOS est rafraîchi si nécessaire, bien que pour du contenu statique
    // chargé au début, ce ne soit généralement pas requis.
    // if (typeof AOS !== 'undefined') {
    //     AOS.refresh();
    // }

}); // Fin de DOMContentLoaded

})();