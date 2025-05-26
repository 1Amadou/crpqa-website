<?php

namespace Database\Seeders;

use App\Models\Researcher;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResearcherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Researchers...');

        // Associer des chercheurs aux utilisateurs existants ayant le rôle "Chercheur"
        $researcherUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Chercheur');
        })->whereDoesntHave('researcherProfile')->get();

        foreach ($researcherUsers as $user) {
            Researcher::factory()->create([
                'user_id' => $user->id,
                'slug' => strtolower(str_replace(' ', '-', $user->name)), // Génère un slug unique
                'first_name_fr' => strtok($user->name, ' '), 
                'first_name_en' => strtok($user->name, ' '), 
                'last_name_fr' => strstr($user->name, ' ') ? trim(strstr($user->name, ' ')) : $user->name, 
                'last_name_en' => strstr($user->name, ' ') ? trim(strstr($user->name, ' ')) : $user->name, 
                'email' => $user->email,
                'is_active' => true,
            ]);
            $this->command->line("Researcher profile created and linked for user: {$user->name}");
        }

        // Créer des profils de chercheurs supplémentaires
        $numberOfAdditionalResearchers = 5 - $researcherUsers->count();
        if ($numberOfAdditionalResearchers > 0) {
            Researcher::factory()->count($numberOfAdditionalResearchers)->create();
            $this->command->line("Created {$numberOfAdditionalResearchers} additional researcher profiles.");
        }

        // Création manuelle d'un chercheur spécifique
        Researcher::factory()->create([
            'slug' => 'marie-curie',
            'first_name_fr' => 'Marie',
            'first_name_en' => 'Marie',
            'last_name_fr' => 'Curie',
            'last_name_en' => 'Curie',
            'email' => 'marie.curie@example.com',
            'title_position_fr' => 'Prof. Dr.',
            'title_position_en' => 'Prof. Dr.',
            'biography_fr' => '<p>Pionnière dans le domaine de la radioactivité et double lauréate du prix Nobel.</p>',
            'biography_en' => '<p>Pioneer in the field of radioactivity and two-time Nobel Prize winner.</p>',
            'research_interests_fr' => 'Physique Quantique, Radioactivité, Chimie',
            'research_interests_en' => 'Quantum Physics, Radioactivity, Chemistry',
            'linkedin_url' => 'https://linkedin.com/in/marie-curie',
            'google_scholar_url' => 'https://scholar.google.com/citations?user=MarieCurie',
            'is_active' => true,
            'user_id' => User::where('email', 'mariecurie.user@example.com')->first()?->id,
        ]);
        $this->command->line("Manually created researcher: Marie Curie");

        // Vérifier le chercheur par défaut
        $userChercheur = User::where('email', 'chercheur@crpqa.ml')->first();
        if ($userChercheur && !$userChercheur->researcherProfile) {
            Researcher::factory()->create([
                'user_id' => $userChercheur->id,
                'slug' => 'chercheur-crpqa',
                'first_name_fr' => 'Chercheur',
                'first_name_en' => 'Researcher',
                'last_name_fr' => 'CRPQA',
                'last_name_en' => 'CRPQA',
                'email' => $userChercheur->email,
                'title_position_fr' => 'Chercheur Principal',
                'title_position_en' => 'Principal Researcher',
                'is_active' => true,
            ]);
            $this->command->line("Researcher profile created and linked for default user: {$userChercheur->name}");
        }

        $this->command->info('Researchers seeding completed.');
    }
}
