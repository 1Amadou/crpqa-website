// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js', // Votre point d'entrée JS principal
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/tinymce/skins/ui/oxide', // Skin par défaut (UI)
                    dest: 'assets/tinymce/skins/ui'
                },
                {
                    src: 'node_modules/tinymce/skins/content/default', // Contenu CSS par défaut de l'éditeur
                    dest: 'assets/tinymce/skins/content'
                },
                {
                    src: 'node_modules/tinymce/icons/default', // Icônes par défaut
                    dest: 'assets/tinymce/icons'
                },
                // Copier les plugins essentiels ou ceux que vous prévoyez d'utiliser
                // Il est préférable de copier individuellement les plugins nécessaires
                // plutôt que tout le dossier pour optimiser la taille.
                // Exemple de copie de plugins spécifiques :
                { src: 'node_modules/tinymce/plugins/advlist', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/autolink', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/lists', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/link', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/image', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/charmap', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/preview', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/anchor', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/searchreplace', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/visualblocks', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/code', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/fullscreen', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/insertdatetime', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/media', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/table', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/wordcount', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/autoresize', dest: 'assets/tinymce/plugins' },
                { src: 'node_modules/tinymce/plugins/paste', dest: 'assets/tinymce/plugins' },
                // Fin exemple plugins spécifiques
                {
                    src: 'node_modules/tinymce/themes/silver/theme.min.js', // Thème Silver JS
                    dest: 'assets/tinymce/themes/silver'
                },
                // Fichier de langue français (si vous l'utilisez)
                {
                    src: 'node_modules/tinymce/langs/fr_FR.js',
                    dest: 'assets/tinymce/langs'
                }
            ],
            // Option pour structurer la sortie :
            // structured: true, // si vous préférez garder la structure de node_modules/tinymce/xyz dans public/assets/tinymce/xyz
        })
    ],
});