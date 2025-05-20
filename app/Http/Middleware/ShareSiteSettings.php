<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; // Importer View
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Schema;

class ShareSiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ne pas exécuter pour les routes admin pour éviter des requêtes inutiles si non requis
        if (!$request->is('admin/*') && !$request->is('admin')) {
            try {
                // Récupérer la première (et unique) ligne de paramètres
                // Utiliser le cache pour la performance sur les pages publiques
                $settings = cache()->rememberForever('site_settings', function () {
                    return SiteSetting::firstOrCreate(['id' => 1]);
                });
                View::share('siteSettings', $settings);
            } catch (\Exception $e) {
                // Gérer l'erreur si la base de données n'est pas accessible, etc.
                // Pour l'instant, on ne fait rien, $siteSettings sera null dans les vues
                View::share('siteSettings', null);
            }
        }
        return $next($request);
    }
}