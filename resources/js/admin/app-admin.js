// resources/js/admin/app-admin.js

// --- Imports pour TinyMCE ---
// Importer le core de TinyMCE
import tinymce from 'tinymce/tinymce';



// Important: Ne pas importer les plugins ici !
// tinyMCE will load them dynamically from the 'plugins' directory.

// ... Les imports de skins et contenu TinyMCE sont gérés par ta config Vite (via 'copy' plugin)
// import 'tinymce/skins/ui/oxide/skin.min.css';
// import 'tinymce/skins/content/default/content.min.css';
// import 'tinymce/skins/ui/oxide-dark/skin.min.css';
// import 'tinymce/skins/content/dark/content.min.css';
// ... (Assure-toi que ces CSS sont bien importés via `resources/css/app.css` ou similaires si tu utilises Tailwind pour les styles)


// Initialize TinyMCE
function initTinyMCE() {
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE n\'est pas chargé. Assurez-vous que le script principal de TinyMCE est inclus ou que Vite le gère correctement.');
        return;
    }

    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    tinymce.init({
        selector: 'textarea.tinymce-editor',
        plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table emoticons hr template paste directionality', // Garde les noms des plugins ici
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | code fullscreen',
        height: 400,

        // Configuration des skins et CSS de contenu
        skin: isDarkMode ? 'oxide-dark' : 'oxide',
        content_css: isDarkMode ? 'dark' : 'default',

        // Ce chemin est crucial pour que TinyMCE trouve ses plugins et sa langue
        // Il doit pointer vers le dossier 'tinymce' que tu as copié dans 'public/assets'
        base_url: '/assets/tinymce', // <-- ASSURE-TOI QUE C'EST BIEN CELA
        suffix: '.min', // Pour utiliser les versions minifiées des plugins

        // Chemins vers les assets de TinyMCE
        language_url: document.head.querySelector('meta[name="tinymce-lang-url"]').content,
        language: document.documentElement.lang.replace('-', '_'), // ex: 'fr_FR'

        // ... autres configurations ...
    });
}
// --- Fonctions utilitaires ---

/**
 * Convertit une chaîne de texte en un slug compatible URL.
 * @param {string} text Le texte à slugifier.
 * @returns {string} Le slug généré.
 */
function slugify(text) {
    if (typeof text !== 'string') return '';
    return text.toString().toLowerCase()
        .normalize('NFKD').replace(/[\u0300-\u036f]/g, '') // Supprime les diacritiques
        .trim() // Supprime les espaces blancs au début et à la fin
        .replace(/\s+/g, '-') // Remplace les espaces par des tirets
        .replace(/[^\w\-]+/g, '') // Supprime tous les caractères non alphanumériques sauf les tirets
        .replace(/\-\-+/g, '-') // Remplace les tirets multiples par un seul
        .replace(/^-+/, '') // Supprime les tirets au début
        .replace(/-+$/, ''); // Supprime les tirets à la fin
}

/**
 * Initialise le générateur de slug automatique pour un champ de titre donné.
 * Le slug n'est mis à jour automatiquement que s'il n'a pas été modifié manuellement.
 * @param {string} titleInputId L'ID de l'élément input du titre.
 * @param {string} slugInputId L'ID de l'élément input du slug.
 * @param {string} defaultLocale La locale par défaut pour le titre si plusieurs langues sont gérées.
 */
function initializeSlugGenerator(titleInputId, slugInputId, defaultLocale = 'fr') {
    const titleInput = document.getElementById(titleInputId);
    const slugInput = document.getElementById(slugInputId);

    if (!titleInput || !slugInput) {
        // console.warn(`Slug generator: Title input '${titleInputId}' or Slug input '${slugInputId}' not found.`);
        return;
    }

    let slugManuallyEdited = slugInput.value.trim() !== '';

    // Si le slug est vide, le générer initialement à partir du titre
    if (slugInput.value.trim() === '' && titleInput.value.trim() !== '') {
        slugInput.value = slugify(titleInput.value);
        slugManuallyEdited = false; // Il est auto-généré, donc pas manuellement édité
    }

    // Capture la valeur initiale du slug pour déterminer si elle est auto-générée
    const initialSlugValue = slugInput.value;
    const initialTitleValue = titleInput.value;

    titleInput.addEventListener('input', function () { // 'input' est mieux que 'keyup' pour coller/couper
        // Si le slug n'a pas été édité manuellement OU si l'utilisateur efface le slug pour qu'il soit regénéré
        // ou si le slug est actuellement identique au slug généré à partir de l'ancien titre, on auto-génère
        if (!slugManuallyEdited || slugInput.value === slugify(initialTitleValue)) {
            slugInput.value = slugify(this.value);
        }
    });

    slugInput.addEventListener('input', function () {
        // Dès que l'utilisateur tape dans le champ slug, on considère qu'il est édité manuellement
        if (this.value.trim() !== slugify(titleInput.value)) {
            slugManuallyEdited = true;
        } else {
            // Si le slug est vide ou correspond à la version slugifiée du titre actuel,
            // on peut le considérer comme non-manuellement édité pour qu'il se regénère.
            slugManuallyEdited = false;
        }
    });
}

/**
 * Initialise la prévisualisation d'une image pour un input de type fichier.
 * @param {string} fileInputId L'ID de l'élément input de type fichier.
 * @param {string} imagePreviewId L'ID de l'élément img pour la prévisualisation.
 */
function initializeImagePreview(fileInputId, imagePreviewId) {
    const fileInput = document.getElementById(fileInputId);
    const imagePreview = document.getElementById(imagePreviewId);

    if (!fileInput || !imagePreview) {
        // console.warn(`Image preview: File input '${fileInputId}' or Image preview '${imagePreviewId}' not found.`);
        return;
    }

    fileInput.addEventListener('change', function (event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        } else {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
        }
    });
}

/**
 * Gère l'affichage conditionnel d'une section en fonction des rôles d'utilisateur sélectionnés.
 */
function initializeUserFormConditionalDisplay() {
    const rolesCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const researcherAssignmentSection = document.getElementById('researcher_assignment_section');
    const researcherRoleName = 'Chercheur'; // Assurez-vous que c'est le nom exact du rôle dans votre base de données/config

    if (!rolesCheckboxes.length || !researcherAssignmentSection) {
        // console.warn('User form conditional display: Roles checkboxes or researcher assignment section not found.');
        return;
    }

    function toggleResearcherSectionDisplay() {
        let researcherRoleIsSelected = Array.from(rolesCheckboxes).some(cb => cb.value === researcherRoleName && cb.checked);
        researcherAssignmentSection.style.display = researcherRoleIsSelected ? 'block' : 'none';
    }

    rolesCheckboxes.forEach(cb => cb.addEventListener('change', toggleResearcherSectionDisplay));
    toggleResearcherSectionDisplay(); // Appel initial pour définir l'état au chargement de la page
}

/**
 * Initialise les onglets horizontaux (souvent utilisés pour les traductions).
 * @param {string} containerSelector Le sélecteur CSS du conteneur des onglets (ex: '#languageTabsContainer').
 * @param {string} tabButtonSelector Le sélecteur CSS des boutons d'onglet (ex: 'button[data-tabs-target]').
 * @param {string} tabContentSelector Le sélecteur CSS des contenus d'onglet (ex: '.tab-pane').
 */
function initHorizontalTabs(containerSelector, tabButtonSelector, tabContentSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) {
        // console.log('Tab container not found:', containerSelector);
        return;
    }

    const tabs = container.querySelectorAll(tabButtonSelector);
    const tabContents = document.querySelectorAll(tabContentSelector); // Sélectionne tous les contenus une fois

    if (tabs.length === 0) {
        // console.log('No tabs found in container:', containerSelector);
        return;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault(); // Empêche le comportement par défaut des liens si c'est un <a>

            // Désactiver tous les onglets
            tabs.forEach(t => {
                t.classList.remove('border-blue-500', 'text-blue-600', 'active');
                t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                t.setAttribute('aria-selected', 'false');
            });

            // Masquer tous les contenus
            tabContents.forEach(c => {
                c.classList.add('hidden');
            });

            // Activer l'onglet cliqué
            this.classList.add('border-blue-500', 'text-blue-600', 'active');
            this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
            this.setAttribute('aria-selected', 'true');

            // Afficher le contenu correspondant
            const target = document.querySelector(this.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // Activer le premier onglet par défaut pour cet ensemble d'onglets, s'il y en a.
    // Ou activer l'onglet sélectionné via `aria-selected="true"` si défini dans Blade.
    const activeTab = container.querySelector(`${tabButtonSelector}[aria-selected="true"]`) || tabs[0];
    if (activeTab && !activeTab.classList.contains('active')) { // Évite de re-cliquer si déjà actif
        activeTab.click(); // Simule un clic pour activer
    }
}

/**
 * Initialise les onglets verticaux (utilisés par exemple pour les paramètres du site).
 * Gère aussi l'activation des onglets horizontaux imbriqués.
 * @param {string} navSelector Le sélecteur CSS de la navigation verticale (ex: '#adminSettingsVerticalTabsNav').
 * @param {string} tabLinkSelector Le sélecteur CSS des liens/boutons d'onglet vertical (ex: 'a.vertical-tab-item').
 * @param {string} tabPaneSelector Le sélecteur CSS des panneaux de contenu (ex: '.vertical-tab-pane').
 */
function initVerticalTabs(navSelector, tabLinkSelector, tabPaneSelector) {
    const nav = document.querySelector(navSelector);
    if (!nav) {
        // console.log('Vertical tab navigation not found:', navSelector);
        return;
    }

    const verticalTabs = nav.querySelectorAll(tabLinkSelector);
    const verticalTabPanes = document.querySelectorAll(tabPaneSelector);

    if (verticalTabs.length === 0) {
        // console.log('No vertical tabs found in nav:', navSelector);
        return;
    }

    function activateVerticalTab(tabLink) {
        const targetId = tabLink.dataset.verticalTabTarget;

        verticalTabs.forEach(t => {
            t.classList.remove('active-vertical-tab');
            t.setAttribute('aria-selected', 'false');
        });
        tabLink.classList.add('active-vertical-tab'); // Le style pour active-vertical-tab est dans settings/edit.blade.php @push('styles')
        tabLink.setAttribute('aria-selected', 'true');

        verticalTabPanes.forEach(pane => {
            if (pane.id === targetId) {
                pane.classList.remove('hidden');
                // Initialiser les onglets de langue à l'intérieur du panneau vertical qui vient d'être activé
                // L'ID du conteneur des onglets de langue doit être unique par panneau vertical
                // Exemple: initHorizontalTabs(`#languageTabContent-${targetId}`, `#languageTabs-${targetId} button`, `#languageTabContent-${targetId} > div`);
                // Note: Si tes onglets de langue sont toujours '#languageTabs' et '#languageTabContent',
                // cette ligne pourrait ne pas être nécessaire ou devrait être plus générique.
                // Si les onglets de langue sont toujours au même niveau et chargés par initHorizontalTabs() globalement
                // au DOMContentLoaded, cette ligne ici peut être redondante ou problématique.
                // Je vais le laisser en commentaire pour l'instant, car la logique globale devrait s'en occuper.
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

    // Activer l'onglet vertical précédemment sélectionné ou le premier par défaut
    const activeVerticalTabId = localStorage.getItem('activeVerticalSettingsTab');
    let activated = false;
    if (activeVerticalTabId) {
        const activeTabLink = nav.querySelector(`[data-vertical-tab-target="${activeVerticalTabId}"]`);
        if (activeTabLink) {
            activateVerticalTab(activeTabLink);
            activated = true;
        }
    }
    if (!activated && verticalTabs.length > 0) {
        activateVerticalTab(verticalTabs[0]); // Activer le premier par défaut
    }
}


/**
 * Initialise TinyMCE pour toutes les textareas ciblées.
 */
function initTinyMCE() {
    // Vérifie si TinyMCE est chargé
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE n\'est pas chargé. Assurez-vous que le script principal de TinyMCE est inclus avant app-admin.js ou que Vite le gère correctement.');
        return;
    }

    tinymce.init({
        selector: 'textarea.tinymce-editor', // Utilise la classe '.tinymce-editor' comme convenu
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table directionality emoticons template paste hr importcss autosave codesample accordion quickbars help', // Mettre à jour avec tous les plugins importés
        toolbar: 'undo redo | styleselect | blocks | bold italic underline strikethrough | align numlist bullist | link image media table | accordion accordionremove | charmap emoticons | codesample fullscreen preview | save print | pagebreak anchor | ltr rtl | help', // Ajuste la barre d'outils
        height: 400, // Hauteur par défaut

        // Très important pour Vite: Dire à TinyMCE de ne pas chercher les assets lui-même
        skin: (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
        content_css: (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),

        // Pour les langues, si tu as copié les fichiers de langue dans public/assets/tinymce/langs/
        language_url: '/assets/tinymce/langs/' + document.documentElement.lang.replace('-', '_') + '.js',
        language: document.documentElement.lang.replace('-', '_'), // ex: 'fr_FR'

        // Personnalisation des options de TinyMCE
        autosave_ask_before_unload: true,
        autosave_interval: '30s',
        autosave_prefix: '{path}{query}-{id}-',
        autosave_restore_when_empty: false,
        autosave_retention: '2m',
        image_advtab: true,

        // Si tu as un gestionnaire de fichiers personnalisé (comme Laravel File Manager)
        // file_picker_callback: function (cb, value, meta) {
        //     let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
        //     let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
        //     let cmsURL = '/laravel-filemanager?editor=' + meta.fieldname;
        //     if (meta.filetype == 'image') {
        //         cmsURL = cmsURL + "&type=Images";
        //     } else {
        //         cmsURL = cmsURL + "&type=Files";
        //     }
        //     tinymce.activeEditor.windowManager.openUrl({
        //         url: cmsURL,
        //         title: 'File Manager',
        //         width: x * 0.8,
        //         height: y * 0.8,
        //         resizable: 'yes',
        //         close_previous: 'no',
        //         onMessage: (api, message) => {
        //             cb(message.content);
        //         }
        //     });
        // },

        // Assure une initialisation propre même si les éléments sont cachés
        setup: function (editor) {
            editor.on('init', function () {
                // S'assure que l'éditeur est redimensionné correctement si son conteneur est initialement caché
                editor.execCommand('mceRepaint');
            });
        }
    }).then(function(editors) {
        if (editors.length > 0) {
            console.log('TinyMCE initialized for:', editors.map(e => e.id));
        } else {
            console.log('TinyMCE selector matched, but no editors were initialized (check for conflicts or errors).');
        }
    }).catch(function(err) {
        console.error('TinyMCE initialization error:', err);
    });
}

// --- Initialisation globale des scripts admin ---

document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin scripts initializing (app-admin.js)...');

    // **Important**: Vérifie si TinyMCE est chargé avant de l'initialiser
    initTinyMCE();

    // Initialisation des générateurs de slug et prévisualisations d'images
    // Utilise les IDs corrects de tes champs dans les composants Blade
    initializeSlugGenerator('title_fr', 'slug'); // Pour les publications/actualités si le titre est généré depuis le FR
    // Si tes titres traduisibles sont sur des onglets, tu devras peut-être appeler initializeSlugGenerator
    // pour le champ de titre de la langue par défaut (ex: title_fr)

    // Actualités (News)
    initializeSlugGenerator('title_fr', 'news_slug'); // Assumes 'title_fr' is the main title for slug generation
    initializeImagePreview('news_cover_image', 'news_image_preview');

    // Événements (Events)
    initializeSlugGenerator('title_fr', 'event_slug'); // Assumes 'title_fr' for events
    initializeImagePreview('event_cover_image', 'event_image_preview');

    // Publications (Publications)
    initializeSlugGenerator('title_fr', 'publication_slug'); // Assumes 'title_fr' for publications
    // Pas de prévisualisation d'image pour le PDF des publications pour l'instant, mais tu peux ajouter
    // initializeImagePreview('publication_pdf', 'publication_pdf_preview'); si tu as un élément <img> pour cela.

    // Chercheurs (Researchers)
    initializeImagePreview('researcher_photo', 'researcher_photo_preview');

    // Partenaires (Partners)
    initializeImagePreview('partner_logo_input', 'partner_logo_preview');

    // Domaines de Recherche (Research Axes)
    initializeSlugGenerator('name_fr', 'research_axis_slug'); // Assumes 'name_fr' for research axes
    initializeImagePreview('research_axis_cover_image', 'research_axis_image_preview');

    // Paramètres du Site (Site Settings)
    initializeImagePreview('logo', 'site_logo_preview');
    initializeImagePreview('favicon', 'site_favicon_preview');

    // Formulaires Utilisateurs (User Forms)
    initializeUserFormConditionalDisplay();

    // Initialisation des onglets horizontaux (pour les langues)
    // Cible les conteneurs d'onglets spécifiques pour chaque type de ressource si nécessaire
    // Si tu as un composant Blade pour les onglets de langue, il doit avoir un ID unique pour que cette fonction le cible.
    // Par exemple, si ton composant de formulaire de publication est dans `admin.publications.form`,
    // et qu'il inclut un div avec `id="languageTabsContent"`, alors l'appel est:
    initHorizontalTabs('#languageTabContent', '#languageTabs button', '#languageTabContent > div');

    // Si tu as plusieurs formulaires avec des onglets de langue dans la même page,
    // ou si tes onglets sont inclus via des composants, tu dois rendre leurs IDs uniques.
    // Exemple :
    // initHorizontalTabs('#publicationLanguageTabContent', '#publicationLanguageTabs button', '#publicationLanguageTabContent > div');
    // initHorizontalTabs('#newsLanguageTabContent', '#newsLanguageTabs button', '#newsLanguageTabContent > div');

    // Initialisation pour la page des paramètres du site (avec onglets verticaux et horizontaux imbriqués)
    initVerticalTabs('#adminSettingsVerticalTabsNav', '#adminSettingsVerticalTabsNav a.vertical-tab-item', '#adminSettingsVerticalTabContentContainer > div.vertical-tab-pane');
    // Note: initHorizontalTabs est déjà appelé DANS activateVerticalTab pour les onglets de settings,
    // il n'est donc pas nécessaire de le réappeler ici pour les settings.

    console.log('Admin scripts initialization complete.');
});