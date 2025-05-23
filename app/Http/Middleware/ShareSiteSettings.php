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

    public function handle(Request $request, Closure $next)
{
    // Récupérer les settings SANS CACHE pour le test
    $settingsModel = SiteSetting::first(); // Ou SiteSetting::find(1);
    $settingsArray = [];

    if ($settingsModel) {
        $settingsArray = $settingsModel->toArray();
    }

    View::share('siteSettings', $settingsArray);

    return $next($request);
}

    
}