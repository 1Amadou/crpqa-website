<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Publication; 
use App\Policies\PublicationPolicy; 
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // Ligne par défaut
        Publication::class => PublicationPolicy::class, // Ajoutez cette ligne
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //  $this->registerPolicies();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Charger les paramètres du site pour la configuration des e-mails
    // S'assurer que cela ne pose pas de problème lors des migrations initiales
    // ou des commandes console où la DB n'est pas encore prête.
    if (Schema::hasTable('site_settings')) { // Vérifier si la table existe
        $settings = SiteSetting::first();
        if ($settings) {
            if ($settings->default_sender_email && $settings->default_sender_name) {
                Config::set('mail.from.address', $settings->default_sender_email);
                Config::set('mail.from.name', $settings->default_sender_name);
            }
        }
    }
    }
}
