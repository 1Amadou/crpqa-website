<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicPublicationController;
use App\Http\Controllers\PublicResearcherController;
use App\Http\Controllers\PublicResearchAxisController;
use App\Http\Controllers\PublicPartnerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StaticPageController as AdminStaticPageController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ResearcherController as AdminResearcherController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
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
Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/a-propos', [PublicPageController::class, 'about'])->name('public.about');
Route::get('/p/{staticPage:slug}', [PublicPageController::class, 'showStaticPage'])->name('public.page');

Route::get('/actualites', [PublicNewsController::class, 'index'])->name('public.news.index');
Route::get('/actualites/{news:slug}', [PublicNewsController::class, 'show'])->name('public.news.show');

//partie2 actualité

Route::get('/actualites/{newsItem:slug}', [PublicNewsController::class, 'show'])
     ->name('public.news.show');

Route::get('/evenements', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/evenements/{event:slug}', [PublicEventController::class, 'show'])->name('public.events.show');
Route::post('/evenements/{event:slug}/register', [PublicEventController::class, 'register'])->name('public.events.register');

Route::get('/publications', [PublicPublicationController::class, 'index'])->name('public.publications.index');
Route::get('/publications/{publication:slug}', [PublicPublicationController::class, 'show'])->name('public.publications.show');

Route::get('/equipe', [PublicResearcherController::class, 'index'])->name('public.researchers.index');
Route::get('/chercheur/{researcher:slug}', [PublicResearcherController::class, 'show'])->name('public.researchers.show');

Route::get('/domaines-recherche', [PublicResearchAxisController::class, 'index'])->name('public.research_axes.index');
Route::get('/domaines-recherche/{researchAxis:slug}', [PublicResearchAxisController::class, 'show'])->name('public.research_axes.show');

Route::get('/partenaires', [PublicPartnerController::class, 'index'])->name('public.partners.index');

Route::get('/contact', [ContactController::class, 'showForm'])->name('public.contact.form');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('public.contact.submit');

/*
|--------------------------------------------------------------------------
| Routes Admin (auth, verified, permission:access admin panel)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'permission:access admin panel'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::resource('users', UserController::class);
        Route::resource('static-pages', AdminStaticPageController::class);

        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');

        Route::resource('researchers', AdminResearcherController::class);

        Route::resource('publications', AdminPublicationController::class);
        Route::get('/publications/{publication}/download-admin', [AdminPublicationController::class, 'downloadAdminPdf'])
            ->name('publications.downloadAdminPdf');

        Route::resource('news', AdminNewsController::class)->parameters(['news' => 'newsItem']);
        Route::get('news/{newsItem}/publish', [AdminNewsController::class, 'publish'])->name('news.publish');
        Route::get('news/{newsItem}/unpublish', [AdminNewsController::class, 'unpublish'])->name('news.unpublish');

        Route::resource('events', AdminEventController::class);
        Route::prefix('events/{event}')
            ->name('events.')
            ->group(function () {
                Route::get('registrations', [EventRegistrationController::class, 'indexForEvent'])->name('registrations.index');
                Route::get('registrations/export-pdf', [EventRegistrationController::class, 'exportPdf'])->name('registrations.exportPdf');
                Route::get('registrations/export-excel', [EventRegistrationController::class, 'exportExcel'])->name('registrations.exportExcel');
            });
        Route::resource('event-registrations', EventRegistrationController::class)
            ->except(['index', 'create', 'store']);

        Route::resource('partners', AdminPartnerController::class);
        Route::resource('research-axes', AdminResearchAxisController::class);
    });

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}
