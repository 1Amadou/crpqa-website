import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import copy from 'rollup-plugin-copy'; // <-- MODIFIE CETTE LIGNE

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin/app-admin.js',
                'resources/css/style.css',
                'resources/js/public-main.js'
            ],
            refresh: true,
        }),
        copy({
            targets: [
                { src: 'node_modules/tinymce/skins', dest: 'public/assets/tinymce' },
                { src: 'node_modules/tinymce/langs', dest: 'public/assets/tinymce' },
            ],
            hook: 'writeBundle'
        })
    ],
});