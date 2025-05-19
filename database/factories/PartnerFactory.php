<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PartnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Partner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = fake()->unique()->company();
        return [
            'name' => $companyName,
            // Pour le logo, nous allons laisser null pour le moment.
            // Vous pourrez les ajouter manuellement via l'admin.
            // Si vous avez des logos de test dans public/storage/seeders/logos/, vous pourriez faire :
            // 'logo_path' => 'seeders/logos/' . fake()->randomElement(['logo1.png', 'logo2.jpg', ...]),
            'logo_path' => null,
            'website_url' => 'https://www.' . Str::slug(str_replace([' Ltd', ' Inc', ' LLC', '.', ','], '', $companyName)) . '.' . fake()->tld(),
            'description' => '<p>' . fake()->bs() . '</p><p>' . fake()->catchPhrase() . '</p>',
            'type' => fake()->randomElement(['Université', 'Entreprise', 'Institution Gouvernementale', 'Fondation', 'ONG', null]),
            'is_active' => fake()->boolean(90),
            'display_order' => fake()->numberBetween(0, 10),
        ];
    }
}