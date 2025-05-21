<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application; // Importer Application
use App\Models\StaticPage;


class ShareSiteSettings
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Si ce n'est pas une requête admin
        if (!$request->is('admin/*') && !$request->is('admin') && !$request->is('api/*')) {
            if (Schema::hasTable('site_settings')) {
                try {
                    // Définir ou récupérer depuis le conteneur (pour s'assurer que c'est un singleton par requête)
                    if (!$this->app->bound('siteSettings')) { // Vérifier si déjà lié pour cette requête
                        $siteSettingsArray = Cache::rememberForever('site_settings_processed', function () {
                            $settingsFromDb = SiteSetting::all();
                            $processedSettings = [];
                            foreach ($settingsFromDb as $setting) {
                                if (method_exists($setting, 'getLocalizedField')) {
                                    $processedSettings[$setting->key] = $setting->getLocalizedField('value');
                                } else {
                                    $processedSettings[$setting->key] = $setting->value;
                                }
                            }
                            return $processedSettings;
                        });
                        $this->app->instance('siteSettings', $siteSettingsArray); // Lier l'instance au conteneur
                    } else {
                        $siteSettingsArray = $this->app->make('siteSettings');
                    }

                    View::share('siteSettings', $siteSettingsArray);

                } catch (\Exception $e) {
                    Log::error("Erreur ShareSiteSettings lors du chargement/partage des SiteSettings : " . $e->getMessage());
                    $emptySettings = [];
                    View::share('siteSettings', $emptySettings);
                    if (!$this->app->bound('siteSettings')) {
                         $this->app->instance('siteSettings', $emptySettings);
                    }
                }
            } else {
                Log::warning('ShareSiteSettings: La table site_settings n\'existe pas.');
                $emptySettings = [];
                View::share('siteSettings', $emptySettings);
                if (!$this->app->bound('siteSettings')) {
                    $this->app->instance('siteSettings', $emptySettings);
                }
            }
        }
        return $next($request);
    }

    
}