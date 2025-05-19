

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