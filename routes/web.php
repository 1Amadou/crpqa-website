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
use App\Http\Controllers\ContactController; // Assurez-vous qu'il existe ou créez-le

// Contrôleurs Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StaticPageController as AdminStaticPageController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ResearcherController as AdminResearcherController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController; // Alias pour la cohérence si vous le souhaitez
use App\Http\Controllers\Admin\EventController as AdminEventController; // Alias pour la cohérence
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController; // Alias pour la cohérence
use App\Http\Controllers\Admin\ResearchAxisController as AdminResearchAxisController; // Alias pour la cohérence
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/
Route::name('public.')->group(function () {
    Route::get('/', [PublicPageController::class, 'home'])->name('home');

    // Pour les pages statiques comme "À Propos" (slug 'a-propos' par exemple)
    Route::get('/p/{staticPage:slug}', [PublicPageController::class, 'show'])->name('page');

    Route::get('/actualites', [PublicNewsController::class, 'index'])->name('news.index');
    Route::get('/actualites/{news:slug}', [PublicNewsController::class, 'show'])->name('news.show');

    Route::get('/evenements', [PublicEventController::class, 'index'])->name('events.index');
    Route::get('/evenements/{event:slug}', [PublicEventController::class, 'show'])->name('events.show');
    Route::post('/evenements/{event:slug}/register', [PublicEventController::class, 'register'])->name('events.register');


    Route::get('/publications', [PublicPublicationController::class, 'index'])->name('publications.index');
    Route::get('/publications/{publication:slug}', [PublicPublicationController::class, 'show'])->name('publications.show');
    Route::get('/publications/download/{publication}', [PublicPublicationController::class, 'downloadPdf'])->name('publications.download');

    Route::get('/equipe', [PublicResearcherController::class, 'index'])->name('researchers.index');
    Route::get('/chercheur/{researcher:slug}', [PublicResearcherController::class, 'show'])->name('researchers.show');

    Route::get('/domaines-recherche', [PublicResearchAxisController::class, 'index'])->name('research_axes.index');
    Route::get('/domaines-recherche/{researchAxis:slug}', [PublicResearchAxisController::class, 'show'])->name('research_axes.show');

    Route::get('/partenaires', [PublicPartnerController::class, 'index'])->name('partners.index');

    Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
    Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');
});

/*
|--------------------------------------------------------------------------
| Routes d'Administration et d'Authentification
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:access admin panel']) // Assurez-vous que cette permission existe et est assignée
    ->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Devrait utiliser App\Http\Controllers\Admin\DashboardController
    Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::resource('users', UserController::class)->except(['show']); // Devrait utiliser App\Http\Controllers\Admin\UserController
    Route::resource('static-pages', AdminStaticPageController::class);
    Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit'); // Devrait utiliser App\Http\Controllers\Admin\SiteSettingController
    Route::post('settings', [SiteSettingController::class, 'update'])->name('settings.update'); // Idem
    Route::resource('researchers', AdminResearcherController::class);
    Route::resource('publications', AdminPublicationController::class);
    Route::get('/publications/{publication}/download-admin', [AdminPublicationController::class, 'downloadAdminPdf'])->name('publications.downloadAdminPdf');
    Route::resource('news', AdminNewsController::class)->parameters(['news' => 'newsItem']);
    Route::resource('events', AdminEventController::class);
    Route::prefix('events/{event}')->name('events.')->group(function() {
        Route::get('registrations', [EventRegistrationController::class, 'indexForEvent'])->name('registrations.index');
        Route::get('registrations/export-pdf', [EventRegistrationController::class, 'exportPdf'])->name('registrations.exportPdf');
        Route::get('registrations/export-excel', [EventRegistrationController::class, 'exportExcel'])->name('registrations.exportExcel');
        Route::post('registrations/import-excel', [EventRegistrationController::class, 'importExcel'])->name('registrations.importExcel');
    });
    Route::resource('event-registrations', EventRegistrationController::class)->except(['index', 'create', 'store']);
    Route::post('event-registrations/bulk-actions', [EventRegistrationController::class, 'bulkActions'])->name('event-registrations.bulk-actions');
    Route::resource('partners', AdminPartnerController::class);
    Route::resource('research-axes', AdminResearchAxisController::class);
});

if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}