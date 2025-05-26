

// resources/js/admin/app-admin.js

// PAS d'import pour tinymce ici s'il est chargé globalement via <script>
// // import tinymce from 'tinymce/tinymce'; // SUPPRIMÉ/COMMENTÉ

// Les imports suivants pour les thèmes, icônes et plugins sont également commentés
// car TinyMCE devrait les charger via base_url une fois son core (tinymce.min.js) chargé.
// // import 'tinymce/themes/silver/theme.js';
// // import 'tinymce/icons/default/icons.js';
// // import 'tinymce/plugins/advlist/plugin.js';
// // ... et ainsi de suite pour les autres plugins ...


/**
 * Fonction d'initialisation pour TinyMCE.
 */
function initializeRichTextEditor() {
    const textareas = document.querySelectorAll('textarea.wysiwyg-editor');
    if (textareas.length > 0) {
        // Vérifier si tinymce est défini globalement (chargé par la balise <script>)
        if (typeof tinymce !== 'undefined') {
            const assetBaseUrl = '/assets/tinymce'; 

            tinymce.init({
                selector: 'textarea.wysiwyg-editor',
                base_url: assetBaseUrl, // Crucial pour le chargement dynamique des plugins, thèmes, etc.

                // Les plugins sont juste listés ici, TinyMCE les chargera depuis base_url/plugins/nomplugin/plugin.js
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media',
                    'table', 'paste', 'wordcount', 'autoresize'
                ].join(' '),
                toolbar: 'undo redo | styles | bold italic underline | ' +
                         'alignleft aligncenter alignright alignjustify | ' +
                         'bullist numlist outdent indent | link image media | code fullscreen preview',

                // skin_url et language_url utilisent le chemin absolu pour plus de sûreté
                skin_url: '/assets/tinymce/skins/ui/oxide', 
                content_css: '/assets/tinymce/skins/content/default/content.min.css',
                language: 'fr_FR',
                language_url: '/assets/tinymce/langs/fr_FR.js',

                height: 500,
                menubar: 'file edit view insert format tools table help',

                paste_data_images: true,
                paste_as_text: true, // Ou false si vous voulez garder plus de formatage au collage
                image_title: true,
                automatic_uploads: true, 
                file_picker_types: 'image',
                file_picker_callback: function (cb, value, meta) { /* ... votre callback existant ... */ },
                autoresize_bottom_margin: 50,
                min_height: 400,

            }).catch(error => {
                console.error('Erreur lors de l\'initialisation de TinyMCE (depuis global) :', error);
            });
        } else {
            console.error('TinyMCE objet global non trouvé. Vérifiez la balise <script> dans votre layout admin.');
        }
    }
}

// --- SLUG GENERATOR ---
function slugify(text) {
    if (typeof text !== 'string') return '';
    return text.toString().toLowerCase()
        .normalize('NFKD').replace(/[\u0300-\u036f]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
}

function initializeSlugGenerator(titleInputId, slugInputId) {
    const titleInput = document.getElementById(titleInputId);
    const slugInput = document.getElementById(slugInputId);

    if (titleInput && slugInput) {
        let slugManuallyEdited = slugInput.value.trim() !== '' && slugInput.value !== slugify(titleInput.value);
        
        // Stocker la valeur initiale du titre pour comparaison si le slug est vide ou auto-généré
        if (!slugManuallyEdited) {
            titleInput.dataset.oldTitleValue = titleInput.value;
        }

        titleInput.addEventListener('keyup', function () {
            if (!slugManuallyEdited || slugInput.value === slugify(this.dataset.oldTitleValue || '')) {
                const newSlug = slugify(this.value);
                slugInput.value = newSlug;
                // Mettre à jour la "dernière valeur connue du titre" seulement si le slug est auto-généré
                if(!slugManuallyEdited) {
                    this.dataset.oldTitleValue = this.value;
                }
            }
        });

        slugInput.addEventListener('input', function () {
            const currentTitleSlug = slugify(titleInput.value);
            if (this.value.trim() !== '' && this.value !== currentTitleSlug) {
                slugManuallyEdited = true;
            } else if (this.value.trim() === '') {
                slugManuallyEdited = false;
                slugInput.value = currentTitleSlug; // Régénérer à partir du titre si vidé
                titleInput.dataset.oldTitleValue = titleInput.value; // Réinitialiser la base de comparaison
            }
        });
         // Initialisation au chargement de la page
        if (titleInput.value && slugInput.value === '') { // Si titre rempli et slug vide
            const initialSlug = slugify(titleInput.value);
            slugInput.value = initialSlug;
            titleInput.dataset.oldTitleValue = titleInput.value;
            slugManuallyEdited = false;
        }
    }
}

// --- IMAGE PREVIEW ---
function initializeImagePreview(fileInputId, imagePreviewId) {
    const fileInput = document.getElementById(fileInputId);
    const imagePreview = document.getElementById(imagePreviewId);

    if (fileInput && imagePreview) {
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
}

// --- USER FORM CONDITIONAL DISPLAY (Researcher Assignment) ---
function initializeUserFormConditionalDisplay() {
    const rolesCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    // Utiliser un sélecteur commun si l'ID est unifié, sinon gérer les deux
    const researcherAssignmentSection = document.getElementById('researcher_assignment_section'); // Unifié
    const researcherRoleName = 'Chercheur'; // Assurez-vous que c'est le nom exact du rôle

    function toggleResearcherSectionDisplay() {
        if (!researcherAssignmentSection) return;
        let researcherRoleIsSelected = Array.from(rolesCheckboxes).some(cb => cb.value === researcherRoleName && cb.checked);
        researcherAssignmentSection.style.display = researcherRoleIsSelected ? 'block' : 'none';
    }

    if (rolesCheckboxes.length > 0) {
        rolesCheckboxes.forEach(cb => cb.addEventListener('change', toggleResearcherSectionDisplay));
        toggleResearcherSectionDisplay(); // Appel initial
    }
}

// --- DOMContentLoaded ---
// Initialisation de tous les scripts admin lorsque le DOM est prêt
document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin scripts initializing (app-admin.js)...');

    initializeRichTextEditor();

    // Pages Statiques
    initializeSlugGenerator('static_page_title', 'static_page_slug');

    // Actualités
    initializeSlugGenerator('news_title', 'news_slug');
    initializeImagePreview('news_cover_image', 'news_image_preview');

    // Événements
    initializeSlugGenerator('event_title', 'event_slug');
    initializeImagePreview('event_cover_image', 'event_image_preview');

    // Publications
    initializeSlugGenerator('publication_title', 'publication_slug');
    // Pas de prévisualisation d'image pour le PDF des publications pour l'instant

    // Chercheurs
    initializeImagePreview('researcher_photo', 'researcher_photo_preview');

    // Partenaires
    initializeImagePreview('partner_logo_input', 'partner_logo_preview');

    // Domaines de Recherche
    initializeSlugGenerator('research_axis_name', 'research_axis_slug');
    initializeImagePreview('research_axis_cover_image', 'research_axis_image_preview');
    
    // Paramètres du Site
    initializeImagePreview('logo', 'site_logo_preview'); // L'input file a id="logo"
    initializeImagePreview('favicon', 'site_favicon_preview'); // L'input file a id="favicon"

    // Formulaires Utilisateurs
    initializeUserFormConditionalDisplay();

    // Les scripts pour event-registrations.js sont dans leur propre fichier
    // et s'initialisent via leur propre DOMContentLoaded.

    console.log('Admin scripts initialization complete.');
});

// Fonction pour initialiser les onglets horizontaux (pour les langues)
function initHorizontalTabs(containerSelector, tabButtonSelector, tabContentSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) {
        // console.log('Tab container not found:', containerSelector);
        return;
    }

    const tabs = container.querySelectorAll(tabButtonSelector);
    const tabContents = [];
    tabs.forEach(tab => {
        const content = document.querySelector(tab.dataset.tabsTarget);
        if (content) tabContents.push(content);
    });

    if (tabs.length === 0) {
        // console.log('No tabs found in container:', containerSelector);
        return;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            tabs.forEach(t => {
                t.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
                t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                t.setAttribute('aria-selected', 'false');
            });
            tabContents.forEach(c => {
                if(c) c.classList.add('hidden');
            });

            this.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-500', 'dark:border-primary-500', 'active');
            this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
            this.setAttribute('aria-selected', 'true');

            const target = document.querySelector(this.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // Activer le premier onglet par défaut pour cet ensemble
    if (tabs.length > 0) {
        tabs[0].click();
    }
}

// Fonction pour initialiser les onglets verticaux
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
                initHorizontalTabs(`#languageTabContent-${targetId}`, `#languageTabs-${targetId} button`, `#languageTabContent-${targetId} > div`);
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


// Initialisation de TinyMCE
function initTinyMCE() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.wysiwyg', // Cible toutes les textareas avec la classe 'wysiwyg'
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor table advlist lists wordcount help charmap quickbars emoticons accordion',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image media table | accordion accordionremove | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
            height: 400,
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            image_advtab: true,
            // Vous pouvez ajouter des configurations de file_picker_callback ici si nécessaire
            // comme nous l'avions dans les @push('scripts')
            // Assurez-vous que les chemins vers les assets de TinyMCE sont corrects
            // (ils sont généralement gérés par le chargement de tinymce.min.js lui-même si la structure des dossiers est standard)
            language_url: document.head.querySelector('meta[name="tinymce-lang-url"]').content, // '/assets/tinymce/langs/fr_FR.js'
            language: document.documentElement.lang.replace('-', '_'), // ex: 'fr_FR'
            skin_url: document.head.querySelector('meta[name="tinymce-skin-url"]').content, // '/assets/tinymce/skins/ui/oxide'
            content_css_dark: document.head.querySelector('meta[name="tinymce-content-css-dark"]').content, // '/assets/tinymce/skins/content/dark/content.css'
            content_css_default: document.head.querySelector('meta[name="tinymce-content-css-default"]').content, // '/assets/tinymce/skins/content/default/content.css'

            // Détection du mode sombre pour le skin de l'éditeur
            skin: (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
            content_css: (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default')

        });
    } else {
        console.warn('TinyMCE n\'est pas chargé. Assurez-vous que le script principal de TinyMCE est inclus avant app-admin.js ou que Vite le gère correctement.');
    }
}

// Appel des fonctions d'initialisation sur DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin scripts initializing (app-admin.js)...');

    // Dans resources/js/admin/app-admin.js, à l'intérieur de DOMContentLoaded
    initHorizontalTabs('#languageTabContentNewsShow', '#languageTabsNewsShow button', '#languageTabContentNewsShow > div');

    // Initialisation pour la page des paramètres du site (avec onglets verticaux et horizontaux imbriqués)
    initVerticalTabs('#adminSettingsVerticalTabsNav', '#adminSettingsVerticalTabsNav a.vertical-tab-item', '#adminSettingsVerticalTabContentContainer > div.vertical-tab-pane');
    // Note: initHorizontalTabs est appelé DANS activateVerticalTab pour les onglets de settings.

    // Initialisation pour les formulaires de pages statiques (uniquement onglets horizontaux)
    initHorizontalTabs('#languageTabContentStaticPage', '#languageTabsStaticPage button', '#languageTabContentStaticPage > div');

    // Initialisation pour les formulaires de News (si vous utilisez la même structure d'onglets)
    initHorizontalTabs('#languageTabContentNews', '#languageTabsNews button', '#languageTabContentNews > div');
    
    // Initialisation pour les formulaires de Events (si vous utilisez la même structure d'onglets)
    initHorizontalTabs('#languageTabContentEvents', '#languageTabsEvents button', '#languageTabContentEvents > div');

    // ... Ajoutez d'autres initialisations d'onglets pour d'autres modules si nécessaire ...
    // Par exemple pour les chercheurs, publications, etc., si leurs formulaires ont des onglets de langue.
    // initHorizontalTabs('#languageTabContentResearchers', '#languageTabsResearchers button', '#languageTabContentResearchers > div');


    // Initialiser TinyMCE
    initTinyMCE();
    
    console.log('Admin scripts initialization complete.');
});