// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // Assurez-vous que cette ligne est présente
                'resources/js/app.js',
                // Ajoutez ici vos autres points d'entrée JS si nécessaire
                'resources/js/public-main.js',
                'resources/js/admin/app-admin.js',
                
            ],
            refresh: true,
        }),
    ],
    // ... autres configurations ...
});