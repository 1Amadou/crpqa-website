// resources/js/admin/app-admin.js

// --- Imports pour TinyMCE ---
// Le core de TinyMCE doit être chargé via votre configuration Vite ou inclus dans l’HTML.
// Exemple avec Vite (resources/js/app.js ou similaire) :
// import 'tinymce/tinymce';
// Les skins et CSS tierces sont gérés via Vite (par exemple, vous avez copié les dossiers de skins dans public/assets/tinymce).
// Ne pas importer les plugins ici : TinyMCE les chargera dynamiquement depuis le dossier public/assets/tinymce/plugins.

// Example pour charger les CSS de TinyMCE via Vite (resources/css/app.css) :
// @import url('/assets/tinymce/skins/ui/oxide/skin.min.css');
// @import url('/assets/tinymce/skins/content/default/content.min.css');
// @import url('/assets/tinymce/skins/ui/oxide-dark/skin.min.css');
// @import url('/assets/tinymce/skins/content/dark/content.min.css');


// -----------------------------------------------------------------------------
// Fonction utilitaire : Convertir du texte en slug compatible URL
function slugify(text) {
    if (typeof text !== 'string') return '';
    return text
        .toString()
        .toLowerCase()
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')     // Supprime les diacritiques
        .trim()                              // Supprime les espaces en début/fin
        .replace(/\s+/g, '-')                // Remplace les espaces par des tirets
        .replace(/[^\w\-]+/g, '')            // Supprime tout ce qui n'est pas alphanumérique ou tiret
        .replace(/\-\-+/g, '-')              // Remplace plusieurs tirets par un seul
        .replace(/^-+/, '')                  // Supprime les tirets en début
        .replace(/-+$/, '');                 // Supprime les tirets en fin
}

// Initialiser un générateur de slug pour un champ titre et un champ slug
function initializeSlugGenerator(titleInputId, slugInputId, defaultLocale = 'fr') {
    const titleInput = document.getElementById(titleInputId);
    const slugInput = document.getElementById(slugInputId);

    if (!titleInput || !slugInput) {
        // Si on ne trouve pas les éléments, on quitte silencieusement
        return;
    }

    let slugManuallyEdited = slugInput.value.trim() !== '';

    // Initialisation du slug si vide et que le titre a déjà une valeur
    if (slugInput.value.trim() === '' && titleInput.value.trim() !== '') {
        slugInput.value = slugify(titleInput.value);
        slugManuallyEdited = false;
    }

    const initialTitleValue = titleInput.value;

    titleInput.addEventListener('input', function () {
        // Si le slug n’a pas été édité manuellement OU si on a effacé le slug précédent
        // ou s’il correspond exactement au slug généré à partir de l’ancien titre
        if (!slugManuallyEdited || slugInput.value === slugify(initialTitleValue)) {
            slugInput.value = slugify(this.value);
        }
    });

    slugInput.addEventListener('input', function () {
        // Si le slug diffère du slug généré, alors marquer comme édité manuellement
        if (this.value.trim() !== slugify(titleInput.value)) {
            slugManuallyEdited = true;
        } else {
            // Si le slug correspond à la version générée du titre, on peut le regénérer automatiquement
            slugManuallyEdited = false;
        }
    });
}

// Initialiser la prévisualisation d’image pour un input file et un <img> de preview
function initializeImagePreview(fileInputId, imagePreviewId) {
    const fileInput = document.getElementById(fileInputId);
    const imagePreview = document.getElementById(imagePreviewId);

    if (!fileInput || !imagePreview) {
        return;
    }

    fileInput.addEventListener('change', function (event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        } else {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
        }
    });
}

// Affichage conditionnel d’une section selon les rôles cochés
function initializeUserFormConditionalDisplay() {
    const rolesCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const researcherAssignmentSection = document.getElementById('researcher_assignment_section');
    const researcherRoleName = 'Chercheur'; // Nom exact du rôle attendu

    if (!rolesCheckboxes.length || !researcherAssignmentSection) {
        return;
    }

    function toggleResearcherSectionDisplay() {
        const selected = Array.from(rolesCheckboxes).some(cb => cb.value === researcherRoleName && cb.checked);
        researcherAssignmentSection.style.display = selected ? 'block' : 'none';
    }

    rolesCheckboxes.forEach(cb => cb.addEventListener('change', toggleResearcherSectionDisplay));
    toggleResearcherSectionDisplay(); // Affichage initial au chargement
}

// Initialiser les onglets horizontaux (tabs) pour la gestion de contenu multilangue
function initHorizontalTabs(containerSelector, tabButtonSelector, tabContentSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) {
        return;
    }

    const tabs = container.querySelectorAll(tabButtonSelector);
    const tabContents = document.querySelectorAll(tabContentSelector);

    if (tabs.length === 0) {
        return;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();

            // Désactive tous les onglets
            tabs.forEach(t => {
                t.classList.remove('border-blue-500', 'text-blue-600', 'active');
                t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                t.setAttribute('aria-selected', 'false');
            });

            // Masque tous les contenus
            tabContents.forEach(c => c.classList.add('hidden'));

            // Active l’onglet cliqué
            this.classList.add('border-blue-500', 'text-blue-600', 'active');
            this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
            this.setAttribute('aria-selected', 'true');

            // Affiche le contenu lié à cet onglet
            const target = document.querySelector(this.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // Activation du premier onglet (ou celui marqué aria-selected=true)
    const activeTab = container.querySelector(`${tabButtonSelector}[aria-selected="true"]`) || tabs[0];
    if (activeTab && !activeTab.classList.contains('active')) {
        activeTab.click();
    }
}

// Initialiser les onglets verticaux (ex. dans les settings de site)
function initVerticalTabs(navSelector, tabLinkSelector, tabPaneSelector) {
    const nav = document.querySelector(navSelector);
    if (!nav) {
        return;
    }

    const verticalTabs = nav.querySelectorAll(tabLinkSelector);
    const verticalTabPanes = document.querySelectorAll(tabPaneSelector);

    if (verticalTabs.length === 0) {
        return;
    }

    function activateVerticalTab(tabLink) {
        const targetId = tabLink.dataset.verticalTabTarget;

        verticalTabs.forEach(t => {
            t.classList.remove('active-vertical-tab');
            t.setAttribute('aria-selected', 'false');
        });
        tabLink.classList.add('active-vertical-tab');
        tabLink.setAttribute('aria-selected', 'true');

        verticalTabPanes.forEach(pane => {
            if (pane.id === targetId) {
                pane.classList.remove('hidden');
                // Si ce panneau contient des onglets horizontaux imbriqués, on peut les initialiser ici.
                // Exemple : initHorizontalTabs(`#languageTabContent-${targetId}`, `#languageTabs-${targetId} button`, `#languageTabContent-${targetId} > div`);
            } else {
                pane.classList.add('hidden');
            }
        });

        localStorage.setItem('activeVerticalSettingsTab', targetId);
    }

    verticalTabs.forEach(tabLink => {
        tabLink.addEventListener('click', function (event) {
            event.preventDefault();
            activateVerticalTab(this);
        });
    });

    // Récupère l’onglet vertical sélectionné précédemment
    const activeVerticalTabId = localStorage.getItem('activeVerticalSettingsTab');
    let activated = false;
    if (activeVerticalTabId) {
        const savedTabLink = nav.querySelector(`[data-vertical-tab-target="${activeVerticalTabId}"]`);
        if (savedTabLink) {
            activateVerticalTab(savedTabLink);
            activated = true;
        }
    }
    if (!activated && verticalTabs.length > 0) {
        activateVerticalTab(verticalTabs[0]);
    }
}


// -----------------------------------------------------------------------------
// Initialisation de TinyMCE (une seule fonction, pas de doublon)

// Assurez-vous d’avoir chargé le script TinyMCE (par ex. via <script src="/assets/tinymce/tinymce.min.js"></script> dans votre blade ou Vite).
function initTinyMCE() {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE n’est pas chargé. Vérifiez l’inclusion du script TinyMCE ou la configuration Vite.');
        return;
    }

    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    tinymce.init({
        selector: 'textarea.tinymce-editor',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'preview', 'anchor', 'searchreplace', 'visualblocks', 'code',
            'fullscreen', 'insertdatetime', 'media', 'table', 'emoticons',
            'hr', 'template', 'paste', 'directionality', 'wordcount',
            'nonbreaking', 'save', 'codesample', 'accordion', 'quickbars', 'help'
        ].join(' '),
        toolbar: [
            'undo redo | styleselect | blocks | bold italic underline strikethrough',
            '| alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            '| removeformat | link image media table accordion accordionremove',
            '| charmap emoticons codesample fullscreen preview',
            '| save print | pagebreak anchor | ltr rtl | help'
        ].join(' '),
        height: 400,

        // Gestion des skins et du CSS de contenu
        skin: isDarkMode ? 'oxide-dark' : 'oxide',
        content_css: isDarkMode ? 'dark' : 'default',

        // Pour Vite, indiquer que TinyMCE ne doit pas chercher les assets automatiquement
        base_url: '/assets/tinymce',
        suffix: '.min',

        // Configuration des langues
        language_url: `/assets/tinymce/langs/${document.documentElement.lang.replace('-', '_')}.js`,
        language: document.documentElement.lang.replace('-', '_'), // ex: fr_FR

        // Options complémentaires
        autosave_ask_before_unload: true,
        autosave_interval: '30s',
        autosave_prefix: '{path}{query}-{id}-',
        autosave_restore_when_empty: false,
        autosave_retention: '2m',
        image_advtab: true,

        // Exemple de gestionnaire de fichier personnalisé (désactivé par défaut)
        /*
        file_picker_callback: function (cb, value, meta) {
            let x = window.innerWidth || document.documentElement.clientWidth;
            let y = window.innerHeight || document.documentElement.clientHeight;
            let cmsURL = '/laravel-filemanager?editor=' + meta.fieldname;
            if (meta.filetype === 'image') {
                cmsURL += "&type=Images";
            } else {
                cmsURL += "&type=Files";
            }
            tinymce.activeEditor.windowManager.openUrl({
                url: cmsURL,
                title: 'File Manager',
                width: x * 0.8,
                height: y * 0.8,
                resizable: 'yes',
                close_previous: 'no',
                onMessage: function(api, message) {
                    cb(message.content);
                }
            });
        },
        */

        // Réinitialiser l’éditeur si son conteneur était caché au chargement
        setup: function (editor) {
            editor.on('init', function () {
                editor.execCommand('mceRepaint');
            });
        }
    })
    .then(function(editors) {
        if (editors.length > 0) {
            console.log('TinyMCE initialized for:', editors.map(e => e.id));
        } else {
            console.log('TinyMCE selector trouvé, mais aucun éditeur initialisé.');
        }
    })
    .catch(function(err) {
        console.error('Erreur lors de l’initialisation de TinyMCE :', err);
    });
}


// -----------------------------------------------------------------------------
// Initialisation globale des scripts admin

document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin scripts initializing (app-admin.js)...');

    // Initialisation de l’éditeur WYSIWYG TinyMCE
    initTinyMCE();

    // Générateurs de slug et prévisualisations d’image pour les différentes entités :
    // News / Actualités
    initializeSlugGenerator('title_fr', 'news_slug');
    initializeImagePreview('news_cover_image', 'news_image_preview');

    // Events / Événements
    initializeSlugGenerator('title_fr', 'event_slug');
    initializeImagePreview('event_cover_image', 'event_image_preview');

    // Publications
    initializeSlugGenerator('title_fr', 'publication_slug');
    // Si vous souhaitez un aperçu pour le PDF de publication, décommentez :
    // initializeImagePreview('publication_pdf', 'publication_pdf_preview');

    // Chercheurs
    initializeImagePreview('researcher_photo', 'researcher_photo_preview');

    // Partenaires
    initializeImagePreview('partner_logo_input', 'partner_logo_preview');

    // Domaines de recherche
    initializeSlugGenerator('name_fr', 'research_axis_slug');
    initializeImagePreview('research_axis_cover_image', 'research_axis_image_preview');

    // Paramètres du site (logo, favicon)
    initializeImagePreview('logo', 'site_logo_preview');
    initializeImagePreview('favicon', 'site_favicon_preview');

    // Formulaires utilisateurs : affichage conditionnel selon rôle "Chercheur"
    initializeUserFormConditionalDisplay();

    // Onglets horizontaux génériques (pour la gestion multilangue)
    // Utilisez des IDs uniques par type de formulaire si vous avez plusieurs sections avec des tabs
    initHorizontalTabs('#languageTabContent', '#languageTabs button', '#languageTabContent > div');

    // Onglets verticaux pour les paramètres du site (embedded dans settings)
    initVerticalTabs(
        '#adminSettingsVerticalTabsNav',
        '#adminSettingsVerticalTabsNav a.vertical-tab-item',
        '#adminSettingsVerticalTabContentContainer > div.vertical-tab-pane'
    );

    console.log('Admin scripts initialization complete.');
});
