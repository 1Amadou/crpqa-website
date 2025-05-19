// tailwind.config.js
const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                'crpqa-first': 'var(--first-color)', // #0A2A4D
                'crpqa-first-alt': 'var(--first-color-alt)', // #1D2C5A
                'crpqa-first-light': 'var(--first-color-light)', // #3A507B
                'crpqa-second': 'var(--second-color)', // #DDE2E8
                'crpqa-accent-cyan': 'var(--accent-color-cyan)', // #00BFFF
                'crpqa-accent-cyan-alt': 'var(--accent-color-cyan-alt)', // #00A4D3
                'crpqa-accent-gold': 'var(--accent-color-gold)', // #D4AF37
                
                'crpqa-title': 'var(--title-color)', // #2D3748
                'crpqa-text': 'var(--text-color)', // #333333
                'crpqa-text-light': 'var(--text-color-light)', // #6C757D
                'crpqa-body': 'var(--body-color)', // #FFFFFF
                'crpqa-container': 'var(--container-color)', // #F8F9FA
                'crpqa-border': 'var(--border-color)', // #E2E8F0
            },
            fontFamily: {
                sans: ['Open Sans', ...defaultTheme.fontFamily.sans], // Votre --body-font
                display: ['Montserrat', ...defaultTheme.fontFamily.sans], // Votre --title-font
            },
            fontSize: { // Vos tailles de police personnalisées
                'xs': '.75rem', // Tailwind a déjà ça, mais pour référence avec vos noms
                'sm': 'var(--small-font-size)', // .875rem
                'smaller': 'var(--smaller-font-size)', // .813rem
                'base': 'var(--normal-font-size)', // 1rem
                'lg': '1.125rem', // Tailwind
                'xl': '1.25rem', // Tailwind
                '2xl': 'var(--h3-font-size)', // 1.5rem
                '3xl': 'var(--h2-font-size)', // 2rem (pourrait être 1.875rem de tw)
                '4xl': 'var(--h1-font-size)', // 2.8rem (pourrait être 2.25rem de tw)
                '5xl': 'var(--big-font-size)', // 3.5rem (pourrait être 3rem de tw)
                // Ajoutez d'autres si nécessaire pour mapper toutes vos tailles
            },
            spacing: { // Vos espacements personnalisés (si vous voulez les utiliser avec p-crpqa-1, m-crpqa-2, etc.)
                'sp-0.25': 'var(--sp-0-25)',
                'sp-0.5': 'var(--sp-0-5)',
                'sp-0.75': 'var(--sp-0-75)',
                'sp-1': 'var(--sp-1)',
                'sp-1.5': 'var(--sp-1-5)',
                'sp-2': 'var(--sp-2)',
                'sp-2.5': 'var(--sp-2-5)',
                'sp-3': 'var(--sp-3)',
            },
            zIndex: { // Vos z-index personnalisés
                'tooltip': 'var(--z-tooltip)',
                'fixed': 'var(--z-fixed)',
                'modal': 'var(--z-modal)',
                'dropdown': 'var(--z-dropdown)',
            },
            boxShadow: { // Vos ombres personnalisées
                'light-crpqa': 'var(--shadow-light)',
                'medium-crpqa': 'var(--shadow-medium)',
            }
            // Nous ne définissons pas --header-height ou --transition ici, car ce sont des valeurs
            // qui seront utilisées directement dans le CSS ou via des classes utilitaires (pour la transition).
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};