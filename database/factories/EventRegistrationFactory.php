<?php
namespace Database\Factories;
use App\Models\EventRegistration;
  use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        $isRegisteredUser = fake()->boolean(60); // 60% de chance que ce soit un utilisateur enregistré
        $user = null;
        $participantName = fake()->name();
        // Pour s'assurer que l'email est unique pour la paire event_id/email,
        // il est plus sûr de générer un email complètement aléatoire pour les invités,
        // ou de passer l'event_id à la factory pour construire un email unique par rapport à cet événement.
        // Pour la simplicité des seeders aléatoires, un email globalement unique est souvent suffisant.
        $participantEmail = fake()->unique()->safeEmail();

        if ($isRegisteredUser && User::count() > 0) {
            $user = User::inRandomOrder()->first();
            if ($user) {
                $participantName = $user->name;
                // Pour les utilisateurs enregistrés, utiliser leur email.
                // Le seeder devra s'assurer de ne pas les inscrire plusieurs fois au même événement.
                $participantEmail = $user->email; 
            }
        }

        return [
            // event_id sera souvent surchargé par le seeder
            'event_id' => Event::inRandomOrder()->first()?->id ?? Event::factory(), 
            'user_id' => $user?->id,
            'name' => $participantName,
            'email' => $participantEmail,
            'phone_number' => fake()->optional(0.7, null)->phoneNumber(),
            'organization' => fake()->optional(0.5, null)->company(),
            'motivation' => fake()->optional(0.5, null)->sentence(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'attended', 'cancelled']),
            'notes' => fake()->optional(0.3, null)->paragraph(),
            'registered_at' => fake()->dateTimeThisYear(),
        ];
    }

    public function approved(): Factory
    {
        return $this->state(fn (array $attributes) => ['status' => 'approved']);
    }

    public function pending(): Factory
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}