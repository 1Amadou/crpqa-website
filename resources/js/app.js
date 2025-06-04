import './bootstrap';
import '../css/app.css';
import './admin/app-admin.js';
import './admin/event-registrations.js';
import './public-main.js';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'; // Importer le plugin
Alpine.plugin(collapse); // Enregistrer le plugin
window.Alpine = Alpine;
Alpine.start();
