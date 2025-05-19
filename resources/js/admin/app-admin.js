// resources/js/admin/app-admin.js

// Import de TinyMCE et de ses dépendances (thème, icônes, plugins)
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver/theme.js';       // Thème Silver (ou .min.js)
import 'tinymce/icons/default/icons.js';       // Icônes par défaut (ou .min.js)

// Importez UNIQUEMENT les plugins TinyMCE que vous allez effectivement utiliser
// et que vous avez configurés pour être copiés dans vite.config.js
import 'tinymce/plugins/advlist/plugin.js';    // Listes avancées
import 'tinymce/plugins/autolink/plugin.js';   // Crée automatiquement des liens
import 'tinymce/plugins/lists/plugin.js';      // Support de base pour les listes
import 'tinymce/plugins/link/plugin.js';       // Pour insérer/modifier des liens
import 'tinymce/plugins/image/plugin.js';      // Pour insérer/modifier des images (gestion basique)
import 'tinymce/plugins/charmap/plugin.js';    // Caractères spéciaux
import 'tinymce/plugins/preview/plugin.js';    // Aperçu du contenu
import 'tinymce/plugins/anchor/plugin.js';     // Pour créer des ancres
import 'tinymce/plugins/searchreplace/plugin.js';// Rechercher/Remplacer
import 'tinymce/plugins/visualblocks/plugin.js';// Montre les blocs HTML
import 'tinymce/plugins/code/plugin.js';       // Voir/Modifier le code source HTML
import 'tinymce/plugins/fullscreen/plugin.js'; // Mode plein écran
import 'tinymce/plugins/insertdatetime/plugin.js';// Insérer date/heure
import 'tinymce/plugins/media/plugin.js';      // Insérer des médias (vidéo, audio)
import 'tinymce/plugins/table/plugin.js';      // Insérer/Modifier des tableaux
import 'tinymce/plugins/wordcount/plugin.js';  // Compteur de mots
import 'tinymce/plugins/autoresize/plugin.js'; // Redimensionne automatiquement l'éditeur
import 'tinymce/plugins/paste/plugin.js';      // Meilleure gestion du copier/coller

/**
 * Fonction d'initialisation pour TinyMCE.
 */
function initializeRichTextEditor() {
    const textareas = document.querySelectorAll('textarea.wysiwyg-editor');
    if (textareas.length > 0) {
        const assetBaseUrl = '/assets/tinymce'; // Doit correspondre à votre 'dest' dans vite.config.js

        tinymce.init({
            selector: 'textarea.wysiwyg-editor',
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste wordcount autoresize'
            ].join(' '),
            toolbar: 'undo redo | styles | bold italic underline | ' +
                     'alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | link image media | code fullscreen preview',
            
            skin_url: `${assetBaseUrl}/skins/ui/oxide`,
            // Utilisez le content_css par défaut du skin pour commencer, c'est plus simple.
            // Vous pourrez créer un CSS personnalisé plus tard si nécessaire.
            content_css: `${assetBaseUrl}/skins/content/default/content.min.css`,
            // Si vous avez des polices Google Fonts chargées dans votre admin,
            // vous pouvez les ajouter ici aussi pour que l'éditeur les utilise :
            // content_style: "@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Montserrat:wght@600;700&display=swap'); body { font-family: 'Open Sans', sans-serif; } h1,h2,h3,h4,h5,h6 {font-family: 'Montserrat', sans-serif;}",


            height: 450, // Hauteur un peu augmentée
            menubar: 'file edit view insert format tools table help',
            autoresize_bottom_margin: 30,
            min_height: 400,
            
            paste_data_images: true, // Permet de coller des images (en base64)
            paste_as_text: true,     // Nettoie le formatage au collage

            image_title: true,
            automatic_uploads: true, 
            file_picker_types: 'image',
            file_picker_callback: function (cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function () {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.onload = function () {
                        // Pour une solution de PROD, implémentez un upload serveur ici.
                        // Exemple basique avec Data URL (non idéal pour grosses images) :
                        cb(reader.result, { title: file.name });
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            },
            // language: 'fr_FR', // Décommentez si fr_FR.js est copié par Vite
            // language_url: `${assetBaseUrl}/langs/fr_FR.js`,
        }).catch(error => {
            console.error('Erreur lors de l\'initialisation de TinyMCE :', error);
        });
    } else {
        // console.log('Aucun textarea .wysiwyg-editor trouvé sur cette page.');
    }
}

/**
 * Générateur de Slug.
 */
function slugify(text) {
    if (typeof text !== 'string') return '';
    return text.toString().toLowerCase()
        .normalize('NFKD').replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, '-').replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '');
}

function initializeSlugGenerator(titleInputId, slugInputId, initialSlug = '', initialTitle = '') {
    const titleInput = document.getElementById(titleInputId);
    const slugInput = document.getElementById(slugInputId);
    if (!titleInput || !slugInput) return;

    let previousTitleSlugValue = initialSlug || slugify(initialTitle || titleInput.value);
    let slugManuallyEdited = (initialSlug !== '' && initialSlug !== slugify(initialTitle || titleInput.value));

    if (slugInput.value && titleInput.value && slugInput.value !== slugify(titleInput.value) && !initialSlug) {
        slugManuallyEdited = true;
        previousTitleSlugValue = slugInput.value;
    } else if (!initialSlug && titleInput.value && slugInput.value === '') {
        const newGeneratedSlug = slugify(titleInput.value);
        slugInput.value = newGeneratedSlug;
        previousTitleSlugValue = newGeneratedSlug;
    }

    titleInput.addEventListener('keyup', function () {
        if (!slugManuallyEdited || slugInput.value === previousTitleSlugValue) {
            const newSlug = slugify(this.value);
            slugInput.value = newSlug;
            previousTitleSlugValue = newSlug;
        }
    });

    slugInput.addEventListener('input', function () {
        const currentTitleSlugValue = slugify(titleInput.value);
        if (this.value !== currentTitleSlugValue) {
            slugManuallyEdited = true;
        }
        if (this.value === '') {
            slugManuallyEdited = false;
            const newSlug = slugify(titleInput.value);
            slugInput.value = newSlug;
            previousTitleSlugValue = newSlug;
        }
    });
}

/**
 * Prévisualisation d'Image.
 */
function initializeImagePreview(fileInputId, imagePreviewId) {
    const fileInput = document.getElementById(fileInputId);
    const imagePreview = document.getElementById(imagePreviewId);
    if (!fileInput || !imagePreview) return;

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
 * Actions Groupées pour Inscriptions aux Événements.
 */
function initializeEventRegistrationsBulkActions() {
    const bulkActionsForm = document.getElementById('bulkActionsForm');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const registrationCheckboxes = document.querySelectorAll('.registration-checkbox');
    if (!bulkActionsForm || !selectAllCheckbox || registrationCheckboxes.length === 0) return;

    selectAllCheckbox.addEventListener('change', () => {
        registrationCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    });
    registrationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (!checkbox.checked) selectAllCheckbox.checked = false;
            else {
                selectAllCheckbox.checked = Array.from(registrationCheckboxes).every(cb => cb.checked);
            }
        });
    });
    bulkActionsForm.addEventListener('submit', function (event) {
        const bulkActionSelect = document.getElementById('bulk_action');
        const selectedAction = bulkActionSelect ? bulkActionSelect.value : '';
        const selectedRegistrations = Array.from(registrationCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (selectedAction === '') { event.preventDefault(); alert('Veuillez sélectionner une action.'); return; }
        if (selectedRegistrations.length === 0) { event.preventDefault(); alert('Veuillez sélectionner au moins une inscription.'); return; }
        if (['delete', 'approve', 'reject'].includes(selectedAction)) {
            let actionText = selectedAction === 'delete' ? 'supprimer' : (selectedAction === 'approve' ? 'approuver' : 'rejeter');
            if (!confirm(`Êtes-vous sûr de vouloir ${actionText} les ${selectedRegistrations.length} inscription(s) sélectionnée(s) ?`)) {
                event.preventDefault();
            }
        }
    });
}

/**
 * Affichage Conditionnel Formulaire Utilisateur.
 */
function initializeUserFormConditionalDisplay() {
    const rolesCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const researcherSectionCreate = document.getElementById('researcher_assignment_section');
    const researcherSectionEdit = document.getElementById('researcher_assignment_section_edit');
    const researcherRoleName = 'Chercheur'; // Assurez-vous que c'est le nom exact du rôle

    function toggleResearcherSectionDisplay(section) {
        if (!section) return;
        let researcherRoleIsSelected = Array.from(rolesCheckboxes).some(cb => cb.value === researcherRoleName && cb.checked);
        section.style.display = researcherRoleIsSelected ? 'block' : 'none';
    }

    if (rolesCheckboxes.length > 0) {
        rolesCheckboxes.forEach(cb => cb.addEventListener('change', () => {
            if(researcherSectionCreate) toggleResearcherSectionDisplay(researcherSectionCreate);
            if(researcherSectionEdit) toggleResearcherSectionDisplay(researcherSectionEdit);
        }));
        // Appel initial
        if(researcherSectionCreate) toggleResearcherSectionDisplay(researcherSectionCreate);
        if(researcherSectionEdit) toggleResearcherSectionDisplay(researcherSectionEdit);
    }
}


// Initialisation Globale au chargement du DOM pour l'admin
document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin scripts initializing...');

    // --- Initialisation pour les formulaires de Pages Statiques ---
    if (document.getElementById('static_page_title') && document.getElementById('static_page_slug')) {
        const slugInput = document.getElementById('static_page_slug');
        const titleInput = document.getElementById('static_page_title');
        initializeSlugGenerator('static_page_title', 'static_page_slug', slugInput.value, titleInput.value);
    }

    // --- Initialisation pour les formulaires d'Actualités ---
    if (document.getElementById('news_title') && document.getElementById('news_slug')) {
        const slugInput = document.getElementById('news_slug');
        const titleInput = document.getElementById('news_title');
        initializeSlugGenerator('news_title', 'news_slug', slugInput.value, titleInput.value);
    }
    if (document.getElementById('news_cover_image') && document.getElementById('news_image_preview')) {
        initializeImagePreview('news_cover_image', 'news_image_preview');
    }

    // --- Initialisation pour les formulaires d'Événements ---
    if (document.getElementById('event_title') && document.getElementById('event_slug')) {
        const slugInput = document.getElementById('event_slug');
        const titleInput = document.getElementById('event_title');
        initializeSlugGenerator('event_title', 'event_slug', slugInput.value, titleInput.value);
    }
    if (document.getElementById('cover_image') && document.getElementById('event_image_preview')) { // Note: 'cover_image' est générique, attention aux conflits d'ID si plusieurs formulaires sur la même page (peu probable en admin)
        initializeImagePreview('cover_image', 'event_image_preview');
    }
    
    // --- Initialisation pour les formulaires de Publications ---
    if (document.getElementById('publication_title') && document.getElementById('publication_slug')) {
        const slugInput = document.getElementById('publication_slug');
        const titleInput = document.getElementById('publication_title');
        initializeSlugGenerator('publication_title', 'publication_slug', slugInput.value, titleInput.value);
    }
    if (document.getElementById('publication_pdf_upload') && document.getElementById('publication_pdf_preview')) { // Supposez ces IDs pour le PDF
        // initializeImagePreview n'est pas pour les PDF, mais on pourrait avoir un script pour afficher le nom du fichier
    }

    // --- Initialisation pour les formulaires de Chercheurs ---
    // Pas de slug pour les chercheurs
    if (document.getElementById('researcher_photo') && document.getElementById('researcher_photo_preview')) {
        initializeImagePreview('researcher_photo', 'researcher_photo_preview');
    }

    // --- Initialisation pour les formulaires de Partenaires ---
    // Pas de slug pour les partenaires
    if (document.getElementById('partner_logo') && document.getElementById('partner_logo_preview')) {
        initializeImagePreview('partner_logo', 'partner_logo_preview');
    }

    // --- Initialisation pour les formulaires de Domaines de Recherche ---
     if (document.getElementById('research_axis_name') && document.getElementById('research_axis_slug')) { // Notez les IDs que nous avons utilisés
        const slugInput = document.getElementById('research_axis_slug');
        const titleInput = document.getElementById('research_axis_name');
        initializeSlugGenerator('research_axis_name', 'research_axis_slug', slugInput.value, titleInput.value);
    }
    if (document.getElementById('research_axis_cover_image') && document.getElementById('research_axis_image_preview')) {
        initializeImagePreview('research_axis_cover_image', 'research_axis_image_preview');
    }

    // --- Initialisation pour le formulaire des Paramètres du Site ---
    if (document.getElementById('site_logo') && document.getElementById('site_logo_preview')) {
        initializeImagePreview('site_logo', 'site_logo_preview');
    }
    if (document.getElementById('site_favicon') && document.getElementById('site_favicon_preview')) {
        initializeImagePreview('site_favicon', 'site_favicon_preview');
    }
    
    // --- Initialisation pour le formulaire des Utilisateurs ---
    if (document.querySelector('input[name="roles[]"]')) {
        initializeUserFormConditionalDisplay();
    }

    // --- Initialisation pour les Actions Groupées des Inscriptions aux Événements ---
    if (document.getElementById('bulkActionsForm')) {
        initializeEventRegistrationsBulkActions();
    }

    // --- Initialisation Globale de TinyMCE ---
    // Doit être appelé après que le DOM soit prêt et que les textarea.wysiwyg-editor existent.
    initializeRichTextEditor();

    console.log('Admin scripts initialized.');
});