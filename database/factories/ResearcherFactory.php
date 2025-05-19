<?php

namespace Database\Factories;

use App\Models\Researcher;
use App\Models\User; // Si vous voulez lier à des utilisateurs existants ou en créer
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResearcherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Researcher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'title' => fake()->randomElement(['Dr.', 'Prof.', null]),
            'position' => fake()->jobTitle(),
            'biography' => '<p>' . implode('</p><p>', fake()->paragraphs(3)) . '</p>', // HTML simple
            'research_areas' => implode(', ', fake()->words(rand(3, 7))),
            // 'photo_path' => null, // Peut être ajouté manuellement ou via un état de factory
            'linkedin_url' => 'https://linkedin.com/in/' . Str::slug($firstName . '-' . $lastName),
            'google_scholar_url' => 'https://scholar.google.com/citations?user=' . Str::random(12),
            'is_active' => fake()->boolean(90), // 90% de chance d'être actif
            'display_order' => fake()->numberBetween(0, 10),
            // 'user_id' => null, // Sera géré dans le seeder si on lie à des utilisateurs
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Researcher $researcher) {
            // Si vous voulez créer un utilisateur lié automatiquement pour chaque chercheur :
            // (Attention, cela peut entrer en conflit avec UserSeeder si vous ne gérez pas bien les rôles)
            /*
            if (!$researcher->user_id) {
                $user = User::factory()->create([
                    'name' => $researcher->first_name . ' ' . $researcher->last_name,
                    'email' => $researcher->email, // S'assurer que l'email est unique aussi dans la table users
                ]);
                $user->assignRole('Chercheur'); // Assurez-vous que le rôle 'Chercheur' existe
                $researcher->user_id = $user->id;
                $researcher->save();
            }
            */
        });
    }
}