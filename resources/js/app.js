// resources/js/app.js
import './bootstrap';
import '../css/app.css'; // CSS global de l'application (Tailwind inclus)

// Nos scripts admin (qui incluent maintenant TinyMCE et les autres fonctions)
import './admin/app-admin.js';
import './admin/event-registrations.js'; // Si vous le gardez séparé pour les actions groupées

// Optionnel: Scripts pour le site public (si vous en avez)
// import './public-main.js'; // Vous l'aviez, assurez-vous qu'il est bien structuré

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();