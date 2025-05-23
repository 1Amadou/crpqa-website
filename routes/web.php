<?php

use Illuminate\Support\Facades\Route;

// Contrôleurs Publics
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicPublicationController;
use App\Http\Controllers\PublicResearcherController;
use App\Http\Controllers\PublicResearchAxisController;
use App\Http\Controllers\PublicPartnerController;
use App\Http\Controllers\ContactController;

// Contrôleurs Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StaticPageController as AdminStaticPageController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ResearcherController as AdminResearcherController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController; // Utiliser cet alias pour l'admin
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\ResearchAxisController as AdminResearchAxisController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/
Route::name('public.')->group(function () {
    // Page d'accueil
    Route::get('/', [PublicPageController::class, 'home'])->name('home');
    
    // Page À propos
    Route::get('/a-propos', [PublicPageController::class, 'about'])->name('about'); // Sera nommée 'public.about'

    // Pages statiques génériques (slug)
    Route::get('/p/{staticPage:slug}', [PublicPageController::class, 'showStaticPage'])->name('page');

    // Actualités publiques
    Route::get('/actualites', [PublicNewsController::class, 'index'])->name('news.index'); // Sera nommée 'public.news.index'
    Route::get('/actualites/{news:slug}', [PublicNewsController::class, 'show'])->name('news.show'); // Sera nommée 'public.news.show'
    Route::get('/actualites/{news}', [PublicNewsController::class, 'show'])->name('public.news.show');
    Route::get('/actualites/{news:slug}', [PublicNewsController::class, 'show'])->name('news.show'); // Devrait créer 'public.news.show'
    
    // Événements publics
    Route::get('/evenements', [PublicEventController::class, 'index'])->name('events.index');
    Route::get('/evenements/{event:slug}', [PublicEventController::class, 'show'])->name('events.show');
    Route::post('/evenements/{event:slug}/register', [PublicEventController::class, 'register'])->name('events.register');
    
    // Publications publiques
    Route::get('/publications', [PublicPublicationController::class, 'index'])->name('publications.index');
    Route::get('/publications/{publication:slug}', [PublicPublicationController::class, 'show'])->name('publications.show');
    Route::get('/publications/download/{publication}', [PublicPublicationController::class, 'downloadPdf'])->name('publications.download');

    // Équipe de chercheurs
    Route::get('/equipe', [PublicResearcherController::class, 'index'])->name('researchers.index');
    Route::get('/chercheur/{researcher:slug}', [PublicResearcherController::class, 'show'])->name('researchers.show');

    // Domaines de recherche
    Route::get('/domaines-recherche', [PublicResearchAxisController::class, 'index'])->name('research_axes.index');
    Route::get('/domaines-recherche/{researchAxis:slug}', [PublicResearchAxisController::class, 'show'])->name('research_axes.show');

    // Partenaires
    Route::get('/partenaires', [PublicPartnerController::class, 'index'])->name('partners.index');

    // Contact
    Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
    Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');
});

/*
|--------------------------------------------------------------------------
| Routes Admin (auth + permission)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'permission:access admin panel']) // Ajout de 'verified' pour la cohérence avec les routes Breeze/Jetstream typiques
    ->name('admin.')
    ->group(function () {
        // Dashboard & Profil
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        // Utilisateurs & pages statiques
        Route::resource('users', UserController::class);
        Route::resource('static-pages', AdminStaticPageController::class);

        // Paramètres du site
        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update');

        // Ressources administratives
        Route::resource('researchers', AdminResearcherController::class);
        
        Route::resource('publications', AdminPublicationController::class);
        Route::get('/publications/{publication}/download-admin', [AdminPublicationController::class, 'downloadAdminPdf'])->name('publications.downloadAdminPdf');
        
        // News Management (UNE SEULE DÉFINITION CORRECTE ICI)
        Route::resource('news', AdminNewsController::class)->parameters(['news' => 'newsItem']);
        // Les routes personnalisées pour publish/unpublish doivent être DANS LE GROUPE DE NOM 'admin.' et avec un nom simple
        Route::get('news/{newsItem}/publish', [AdminNewsController::class, 'publish'])->name('news.publish');
        Route::get('news/{newsItem}/unpublish', [AdminNewsController::class, 'unpublish'])->name('news.unpublish');

        Route::resource('events', AdminEventController::class);
        Route::prefix('events/{event}')->name('events.')->group(function () {
            Route::get('registrations', [EventRegistrationController::class, 'indexForEvent'])->name('registrations.index');
            Route::get('registrations/export-pdf', [EventRegistrationController::class, 'exportPdf'])->name('registrations.exportPdf');
            Route::get('registrations/export-excel', [EventRegistrationController::class, 'exportExcel'])->name('registrations.exportExcel');
            // Route::post('registrations/import-excel', [EventRegistrationController::class, 'importExcel'])->name('registrations.importExcel'); // Assurez-vous que cette méthode existe et fonctionne
        });
        Route::resource('event-registrations', EventRegistrationController::class)->except(['index','create','store']);
        // Route::post('event-registrations/bulk-actions', [EventRegistrationController::class, 'bulkActions']); // Nom avec tiret, préférez underscore: bulk_actions

        Route::resource('partners', AdminPartnerController::class);
        Route::resource('research-axes', AdminResearchAxisController::class);
    });

// Routes d'authentification (Breeze, Jetstream, etc.)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}