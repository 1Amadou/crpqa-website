<?php

use Illuminate\Support\Facades\Route;

// --- Contrôleurs Publics ---
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicPublicationController;
// use App\Http\Controllers\PublicResearcherController; // Si vous en créez un

// --- Contrôleurs Admin ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\ResearcherController;
use App\Http\Controllers\Admin\PublicationController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ProfileController; 
use App\Http\Controllers\Admin\ResearchAxisController; 

/*
|--------------------------------------------------------------------------
| Routes Publiques du Site
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/page/{slug}', [PublicPageController::class, 'showStaticPage'])->name('public.static.page');

Route::get('/actualites', [PublicNewsController::class, 'index'])->name('public.news.index');
Route::get('/actualites/{slug}', [PublicNewsController::class, 'show'])->name('public.news.show');

Route::get('/publications', [PublicPublicationController::class, 'index'])->name('public.publications.index');
Route::get('/publications/{slug}', [PublicPublicationController::class, 'show'])->name('public.publications.show');

Route::get('/evenements', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/evenements/{slug}', [PublicEventController::class, 'show'])->name('public.events.show');
Route::get('/evenements/{event_slug}/inscription', [PublicEventController::class, 'showRegistrationForm'])->name('public.events.registration.create');
Route::post('/evenements/{event_slug}/inscription', [PublicEventController::class, 'storeRegistration'])->name('public.events.registration.store');

// TODO: Ajouter les autres routes publiques (Chercheurs, Partenaires, Contact, etc.)

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
*/
// Assurez-vous que vos routes d'authentification sont bien gérées (souvent via bootstrap/app.php ou un fichier auth.php)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

/*
|--------------------------------------------------------------------------
| Routes du Panneau d'Administration
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'permission:access admin panel']) // Assurez-vous d'avoir cette permission pour vos rôles admin
    ->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion du Profil Utilisateur Admin Connecté
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Modules de Gestion
    Route::resource('static-pages', StaticPageController::class);
    Route::resource('researchers', ResearcherController::class);
    Route::resource('publications', PublicationController::class);
    Route::resource('news', AdminNewsController::class);
    Route::resource('events', AdminEventController::class);

    // Inscriptions aux Événements
    Route::get('events/{event}/registrations', [EventRegistrationController::class, 'indexForEvent'])->name('events.registrations.index');
    Route::get('events/{event}/registrations/create', [EventRegistrationController::class, 'create'])->name('events.registrations.create');
    Route::post('events/{event}/registrations', [EventRegistrationController::class, 'store'])->name('events.registrations.store');
    Route::get('events/{event}/registrations/export/excel', [EventRegistrationController::class, 'exportExcel'])->name('events.registrations.export.excel');
    Route::get('events/{event}/registrations/export/pdf', [EventRegistrationController::class, 'exportPdf'])->name('events.registrations.export.pdf');
    Route::post('events/{event}/registrations/import/excel', [EventRegistrationController::class, 'importExcel'])->name('events.registrations.import.excel');
    Route::get('event-registrations/{registration}', [EventRegistrationController::class, 'show'])->name('event-registrations.show');
    Route::get('event-registrations/{registration}/edit', [EventRegistrationController::class, 'edit'])->name('event-registrations.edit');
    Route::put('event-registrations/{registration}', [EventRegistrationController::class, 'update'])->name('event-registrations.update');
    Route::delete('event-registrations/{registration}', [EventRegistrationController::class, 'destroy'])->name('event-registrations.destroy');
    Route::post('event-registrations/bulk-actions', [EventRegistrationController::class, 'bulkActions'])->name('event-registrations.bulk-actions');

    Route::resource('partners', PartnerController::class);
    Route::resource('users', UserController::class); // Géré par Super Admin

    // Paramètres du Site
    Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SiteSettingController::class, 'update'])->name('settings.update');

    // Gestion des Domaines de Recherche (NOUVEAU)
    Route::resource('research-axes', ResearchAxisController::class);
});