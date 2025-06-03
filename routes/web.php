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
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\ResearchAxisController as AdminResearchAxisController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RoleController; // Assurez-vous que ce contrôleur existe
use App\Http\Controllers\Admin\PermissionController; // Assurez-vous que ce contrôleur existe
use App\Http\Controllers\Admin\NewsCategoryController; 
/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/a-propos', [PublicPageController::class, 'about'])->name('public.about'); // Exemple, adaptez à votre page statique
Route::get('/page/{staticPage:slug}', [PublicPageController::class, 'showStaticPage'])->name('public.page');

Route::get('/actualites', [PublicNewsController::class, 'index'])->name('public.news.index');
// La route ci-dessous est dupliquée et utilise 'newsItem' au lieu de 'news' comme paramètre.
// Je vais la commenter car la première définition avec {news:slug} est correcte et utilise le bon modèle.
// Route::get('/actualites/{newsItem:slug}', [PublicNewsController::class, 'show'])->name('public.news.show'); 
Route::get('/actualites/{news:slug}', [PublicNewsController::class, 'show'])->name('public.news.show');


Route::get('/evenements', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/evenements/{event:slug}', [PublicEventController::class, 'show'])->name('public.events.show');
Route::post('/evenements/{event:slug}/register', [PublicEventController::class, 'register'])->name('public.events.register');

Route::get('/publications', [PublicPublicationController::class, 'index'])->name('public.publications.index');
Route::get('/publications/{publication:slug}', [PublicPublicationController::class, 'show'])->name('public.publications.show');

Route::get('/equipe', [PublicResearcherController::class, 'index'])->name('public.researchers.index');
// Note: le paramètre de route pour les chercheurs est 'researcher:slug' pour la cohérence
Route::get('/equipe/{researcher:slug}', [PublicResearcherController::class, 'show'])->name('public.researchers.show');

Route::get('/domaines-recherche', [PublicResearchAxisController::class, 'index'])->name('public.research_axes.index');
Route::get('/domaines-recherche/{researchAxis:slug}', [PublicResearchAxisController::class, 'show'])->name('public.research_axes.show');

Route::get('/partenaires', [PublicPartnerController::class, 'index'])->name('public.partners.index');
// Si vous ajoutez une page de détail pour les partenaires avec slug :
// Route::get('/partenaires/{partner:slug}', [PublicPartnerController::class, 'show'])->name('public.partners.show');

Route::get('/contact', [ContactController::class, 'showForm'])->name('public.contact.form');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('public.contact.submit');


/*
|--------------------------------------------------------------------------
| Routes Admin (Protégées)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'permission:access admin panel']) // Permission générale pour accéder au panel
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        // Gestion des Utilisateurs, Rôles, Permissions
        Route::resource('users', UserController::class)->middleware(['permission:manage users']);
        Route::resource('roles', RoleController::class)->middleware(['permission:manage roles']); // Assurez-vous d'avoir RoleController
        // Pour les permissions, la gestion se fait souvent via les rôles ou des commandes,
        // mais si vous avez un CRUD pour les permissions :
        // Route::resource('permissions', PermissionController::class)->middleware(['permission:manage roles']);


        Route::resource('static-pages', AdminStaticPageController::class)->middleware(['permission:manage static_pages|view static_pages']);
        // Si vous voulez des permissions plus fines :
        // Route::resource('static-pages', AdminStaticPageController::class)->except(['index', 'show'])->middleware('permission:manage static_pages');
        // Route::resource('static-pages', AdminStaticPageController::class)->only(['index', 'show'])->middleware('permission:view static_pages');


        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit')->middleware('permission:manage site settings');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update')->middleware('permission:manage site settings');

        // Pour les modèles avec slug, Laravel utilise par défaut le nom de la ressource au singulier pour le paramètre.
        // Ex: pour 'researchers', le paramètre sera {researcher}
        Route::resource('researchers', AdminResearcherController::class)->middleware(['permission:manage researchers|view researchers']);
        Route::resource('publications', AdminPublicationController::class)->middleware(['permission:manage publications|view publications']);
        Route::resource('partners', AdminPartnerController::class)->middleware(['permission:manage partners|view partners']);
        Route::resource('research-axes', AdminResearchAxisController::class)->names('research-axes')->middleware(['permission:manage research_axes|view research_axes']);


        // Actualités - Attention au nom du paramètre de route.
        // Si votre modèle News a getRouteKeyName() qui retourne 'slug', et que le contrôleur attend 'News $newsItem',
        // Laravel essaiera de binder 'newsItem' avec le slug.
        // Route::resource('news', AdminNewsController::class)
        // ->parameters(['news' => 'newsItem']) // Explicite que 'news' dans l'URL doit être 'newsItem' pour le binding
        // ->middleware(['permission:manage news|view news']);
        // Si NewsController utilise News $news (au lieu de News $newsItem):
        Route::resource('news', AdminNewsController::class)->middleware(['permission:manage news|view news']);
        Route::resource('news-categories', NewsCategoryController::class)->names('news-categories'); 
        Route::get('news/{newsItem}/publish', [AdminNewsController::class, 'publish'])->name('news.publish')->middleware('permission:publish news');
        Route::get('news/{newsItem}/unpublish', [AdminNewsController::class, 'unpublish'])->name('news.unpublish')->middleware('permission:publish news');
        // Note: Si vous utilisez News $news dans les méthodes publish/unpublish, changez newsItem en news ici aussi.


        // Événements
        Route::resource('events', AdminEventController::class)->middleware(['permission:manage events|view events']);
        
        // Inscriptions aux Événements
        Route::prefix('events/{event}/registrations') // {event} sera l'ID ou le slug de l'Event
            ->name('events.registrations.')
            ->middleware(['permission:view event_registrations|manage event_registrations']) // Permission pour voir/gérer les inscriptions
            ->controller(EventRegistrationController::class) // PHP 8 group controller syntax
            ->group(function () {
                Route::get('/', 'indexForEvent')->name('index');
                Route::get('/create', 'createForEvent')->name('create'); // Si vous ajoutez manuellement pour un événement spécifique
                Route::post('/', 'storeForEvent')->name('store');    // Si vous ajoutez manuellement pour un événement spécifique
                Route::get('/export-pdf', 'exportPdf')->name('export.pdf')->middleware('permission:export event_registrations');
                Route::get('/export-excel', 'exportExcel')->name('export.excel')->middleware('permission:export event_registrations');
                Route::post('/import-excel', 'importExcel')->name('import.excel')->middleware('permission:import event_registrations');
            });
        
        // Actions individuelles pour les inscriptions (Show, Edit, Update, Destroy)
        // Ces routes n'ont pas besoin du préfixe /events/{event}/ si elles prennent $registration directement
        Route::resource('event-registrations', EventRegistrationController::class)
            ->except(['index', 'create', 'store']) // Celles-ci sont gérées par le groupe ci-dessus
            ->middleware(['permission:manage event_registrations|view event_registrations']);
            
        // Route pour les actions groupées sur les inscriptions
        Route::post('event-registrations/bulk-actions', [EventRegistrationController::class, 'bulkActions'])
            ->name('event-registrations.bulk-actions')
            ->middleware('permission:manage event_registrations');


        // Pour les téléchargements PDF admin si besoin (exemple pour publications)
        // Route::get('/publications/{publication}/download-admin', [AdminPublicationController::class, 'downloadAdminPdf'])
        //     ->name('publications.downloadAdminPdf')->middleware('permission:view publications');
    });

// Routes d'Authentification (si vous utilisez Laravel Breeze/Jetstream, elles sont généralement incluses par `require __DIR__.'/auth.php';`)
if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}