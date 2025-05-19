import './bootstrap';
import '../css/app.css';

// Nos scripts admin
import './admin/app-admin.js';
import './admin/event-registrations.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
