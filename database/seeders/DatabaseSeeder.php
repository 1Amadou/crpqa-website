<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            ResearcherSeeder::class,
            StaticPageSeeder::class,
            SiteSettingsSeeder::class,
            ResearchAxisSeeder::class, 
            PartnerSeeder::class,      
            NewsCategorySeeder::class, 
            NewsSeeder::class,
            PublicationSeeder::class,
            EventSeeder::class,
            EventRegistrationSeeder::class,
        ]);
    }
}