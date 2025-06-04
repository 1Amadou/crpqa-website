<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Publication;
use App\Policies\PublicationPolicy;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Publication::class => PublicationPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('siteSettings', function () {
            return SiteSetting::first();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Charger les paramètres du site pour la configuration des e-mails
        // Vérifier si la table 'site_settings' existe avant d'accéder aux données
        if (Schema::hasTable('site_settings')) {
            $settings = SiteSetting::first();
            if ($settings && $settings->default_sender_email && $settings->default_sender_name) {
                Config::set('mail.from.address', $settings->default_sender_email);
                Config::set('mail.from.name', $settings->default_sender_name);
            }
        }
    }
}
