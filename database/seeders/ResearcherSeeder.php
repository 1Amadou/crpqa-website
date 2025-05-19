<?php

namespace Database\Seeders;

use App\Models\Researcher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResearcherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Researchers...');

        // Option 1: Créer des chercheurs et les lier à des utilisateurs existants ayant le rôle "Chercheur"
        $researcherUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Chercheur');
        })->whereDoesntHave('researcherProfile') // Pour ne pas lier si déjà fait
          ->get();

        foreach ($researcherUsers as $user) {
            Researcher::factory()->create([
                'user_id' => $user->id,
                'first_name' => strtok($user->name, ' '), // Essaye de deviner le prénom
                'last_name' => strstr($user->name, ' ') ? trim(strstr($user->name, ' ')) : $user->name, // Essaye de deviner le nom
                'email' => $user->email, // Utilise l'email de l'utilisateur
                // Vous pouvez surcharger d'autres champs ici si nécessaire
            ]);
            $this->command->line("Researcher profile created and linked for user: {$user->name}");
        }

        // Option 2: Créer des profils de chercheurs supplémentaires non liés à des utilisateurs
        // ou pour lesquels vous créerez des utilisateurs plus tard.
        $numberOfAdditionalResearchers = 5 - $researcherUsers->count(); // Pour avoir environ 5 chercheurs au total
        if ($numberOfAdditionalResearchers > 0) {
            Researcher::factory()->count($numberOfAdditionalResearchers)->create();
            $this->command->line("Created {$numberOfAdditionalResearchers} additional researcher profiles.");
        }

        // Exemple de création manuelle d'un chercheur spécifique
        Researcher::factory()->create([
            'first_name' => 'Marie',
            'last_name' => 'Curie',
            'email' => 'marie.curie@example.com',
            'title' => 'Prof. Dr.',
            'position' => 'Directrice de Recherche en Physique Quantique',
            'biography' => '<p>Pionnière dans le domaine de la radioactivité et double lauréate du prix Nobel.</p>',
            'research_areas' => 'Physique Quantique, Radioactivité, Chimie',
            'is_active' => true,
            'display_order' => 0,
            // Lier à un utilisateur existant si vous en avez un pour Marie Curie, sinon laisser user_id à null
            // 'user_id' => User::where('email', 'mariecurie.user@example.com')->first()?->id,
        ]);
        $this->command->line("Manually created researcher: Marie Curie");


        // Assurez-vous que le chercheur créé par UserSeeder est bien pris en compte
        $userChercheur = User::where('email', 'chercheur@crpqa.ml')->first();
        if ($userChercheur && !$userChercheur->researcherProfile) {
             Researcher::factory()->create([
                'user_id' => $userChercheur->id,
                'first_name' => 'Chercheur',
                'last_name' => 'CRPQA',
                'email' => $userChercheur->email,
                'position' => 'Chercheur Principal',
                'is_active' => true,
            ]);
            $this->command->line("Researcher profile created and linked for default user: {$userChercheur->name}");
        }


        $this->command->info('Researchers seeding completed.');
    }
}