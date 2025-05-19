<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Publication; 
use App\Policies\PublicationPolicy; 
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
        //
    }
}
