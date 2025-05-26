<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Schema; // Important pour vérifier l'existence de la table

class ShareSiteSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si la table 'site_settings' existe avant de la requêter
        // Cela évite les erreurs lors des premières migrations (php artisan migrate)
        if (Schema::hasTable('site_settings')) {
            $siteSettings = SiteSetting::first(); // Récupère la première (et unique) ligne comme un objet Modèle

            // Si aucun paramètre n'est trouvé, on peut initialiser un objet vide
            // ou un objet avec des valeurs par défaut pour éviter les erreurs dans les vues.
            // Cependant, le SiteSettingsSeeder devrait normalement en créer un.
            if (!$siteSettings) {
                // Option 1: Créer un objet SiteSetting vide (non sauvegardé)
                // $siteSettings = new SiteSetting();
                // Ou, si vous avez des valeurs par défaut que vous voulez utiliser :
                // $siteSettings = new SiteSetting([
                // 'site_name_fr' => 'Nom du site par défaut',
                // // ... autres champs avec valeurs par défaut
                // ]);

                // Option 2: Logguer une erreur ou une alerte si les paramètres ne sont pas trouvés
                // Log::warning('Site settings not found in database.');
                // Pour l'instant, si $siteSettings est null, les vues devront gérer ce cas (ex: $siteSettings?->getTranslation(...))
            }
            View::share('siteSettings', $siteSettings);
        } else {
            // Si la table n'existe pas, partagez null ou un objet SiteSetting vide par défaut
            // pour éviter des erreurs si le code s'attend toujours à cette variable.
            View::share('siteSettings', new SiteSetting()); // Partage un objet vide pour éviter les erreurs
        }

        return $next($request);
    }
}