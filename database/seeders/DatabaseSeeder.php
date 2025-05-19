<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ResearcherSeeder::class);
        $this->call(StaticPageSeeder::class);
        $this->call(SiteSettingsSeeder::class);
        
        $this->call(ResearchAxisSeeder::class); 
        $this->call(PartnerSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(PublicationSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(EventRegistrationSeeder::class);

        // Ajoutez les appels aux autres seeders que nous allons créer ici au fur et à mesure
        // $this->call(PartnerSeeder::class);
        // $this->call(NewsSeeder::class);
        // $this->call(PublicationSeeder::class);
        // $this->call(EventSeeder::class);
        // $this->call(EventRegistrationSeeder::class);

        $this->command->info('Database seeding finished successfully!');
    }
}