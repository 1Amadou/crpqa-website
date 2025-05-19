<?php
namespace Database\Seeders;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
   use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Event Registrations...');

        $events = Event::all();
        if ($events->isEmpty()) {
            $this->command->warn('No events found to create registrations for. Please seed events first.');
            return;
        }

        $user1 = User::where('email', 'superadmin@crpqa.ml')->first();
        $userChercheur = User::where('email', 'chercheur@crpqa.ml')->first();
        
        $eventSpecifics = [
            'conference-annuelle-sur-les-technologies-quantiques-catq-2025',
            'seminaire-de-recherche-intrication-quantique-et-ses-applications'
        ];
        $event1 = Event::where('slug', $eventSpecifics[0])->first();
        $event2 = Event::where('slug', $eventSpecifics[1])->first();

        // Inscriptions spécifiques (assurons-nous qu'elles n'existent pas déjà)
        if ($event1 && $user1) {
            EventRegistration::firstOrCreate(
                ['event_id' => $event1->id, 'email' => $user1->email], // Clés pour vérifier l'unicité
                [ // Valeurs à insérer si non trouvé
                    'user_id' => $user1->id,
                    'name' => $user1->name,
                    'status' => 'approved',
                    'registered_at' => now()->subDays(rand(1,30)),
                ]
            );
            $this->command->line("Ensured registration for {$user1->name} to {$event1->title}");
        }
        // Inscription du même utilisateur à un autre événement (ceci est permis)
        if ($event2 && $user1) {
            EventRegistration::firstOrCreate(
                ['event_id' => $event2->id, 'email' => $user1->email],
                [
                    'user_id' => $user1->id,
                    'name' => $user1->name,
                    'status' => 'attended',
                    'registered_at' => now()->subDays(rand(1,30)),
                ]
            );
            $this->command->line("Ensured registration for {$user1->name} to {$event2->title}");
        }

        if ($event1 && $userChercheur) {
            EventRegistration::firstOrCreate(
                ['event_id' => $event1->id, 'email' => $userChercheur->email],
                [
                    'user_id' => $userChercheur->id,
                    'name' => $userChercheur->name,
                    'status' => 'pending',
                    'registered_at' => now()->subDays(rand(1,30)),
                ]
            );
            $this->command->line("Ensured registration for {$userChercheur->name} to {$event1->title}");
        }

        $this->command->info('Creating additional random event registrations...');
        foreach ($events as $event) {
            $numberOfRegistrations = rand(0, 3); // Un peu moins pour éviter trop de conflits potentiels
            for ($i = 0; $i < $numberOfRegistrations; $i++) {
                // Pour les créations en masse, on peut laisser la factory gérer l'unicité de l'email
                // ou s'assurer que l'utilisateur créé/choisi n'est pas déjà inscrit.
                // Une approche plus simple est de créer des invités avec des emails uniques
                $isRegisteredUser = fake()->boolean(30); // Moins de chance de prendre un user existant ici
                $userData = [];
                if ($isRegisteredUser && User::count() > 0) {
                    $randomUser = User::inRandomOrder()->first();
                    // Vérifier si cet utilisateur n'est pas déjà inscrit à CET événement
                    if ($randomUser && !EventRegistration::where('event_id', $event->id)->where('email', $randomUser->email)->exists()) {
                        $userData = [
                            'user_id' => $randomUser->id,
                            'name' => $randomUser->name,
                            'email' => $randomUser->email,
                        ];
                    } else {
                        // Tomber en mode invité si l'utilisateur est déjà inscrit ou non trouvé
                        $userData = [
                            'user_id' => null,
                            'name' => fake()->name(),
                            'email' => fake()->unique()->safeEmail(), // Email unique pour cet invité
                        ];
                    }
                } else {
                    $userData = [
                        'user_id' => null,
                        'name' => fake()->name(),
                        'email' => fake()->unique()->safeEmail(),
                    ];
                }

                EventRegistration::factory()->create(array_merge(
                    ['event_id' => $event->id],
                    $userData
                ));
            }
            if ($numberOfRegistrations > 0) {
                $this->command->line("Attempted to create {$numberOfRegistrations} registrations for event: {$event->title}");
            }
        }
        $this->command->info('Event Registrations seeding completed.');
    }
}