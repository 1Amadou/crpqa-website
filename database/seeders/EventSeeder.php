<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Partner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Events...');

        $usta = Partner::where('name', 'Université des Sciences et Techniques Avancées (USTA)')->first();
        $quantumLeap = Partner::where('name', 'QuantumLeap Technologies Inc.')->first();

        $events = [
            [
                'title' => 'Conférence Annuelle sur les Technologies Quantiques (CATQ 2025)',
                'description' => '<p>Rejoignez-nous pour la 5ème édition de la CATQ, où les leaders de la recherche et de l\'industrie se réunissent pour discuter des dernières innovations quantiques.</p><p>Thèmes principaux : Informatique quantique, cryptographie, capteurs et matériaux.</p>',
                'start_datetime' => Carbon::now()->addMonths(2)->setHour(9)->setMinute(0),
                'end_datetime' => Carbon::now()->addMonths(2)->addDays(2)->setHour(17)->setMinute(0),
                'location' => 'Palais des Congrès de Bamako',
                'is_featured' => true,
                'target_audience' => 'Chercheurs, Ingénieurs, Étudiants avancés, Investisseurs',
                'partners_to_attach' => $usta ? [$usta->id] : [],
            ],
            [
                'title' => 'Séminaire de Recherche : Intrication Quantique et ses Applications',
                'description' => '<p>Un séminaire approfondi présenté par le Professeur Invité Dr. Eva Rostova sur les mystères et les applications pratiques de l\'intrication quantique.</p>',
                'start_datetime' => Carbon::now()->addWeeks(3)->setHour(14)->setMinute(30),
                'end_datetime' => Carbon::now()->addWeeks(3)->setHour(16)->setMinute(0),
                'location' => 'CRPQA - Salle de Séminaire Gamma',
                'is_featured' => false,
                'target_audience' => 'Physiciens, Étudiants en Master et Doctorat',
            ],
            [
                'title' => 'Hackathon Quantique : Résolvez les Défis de Demain',
                'description' => '<p>Participez à notre premier hackathon quantique ! Formez une équipe et développez des solutions innovantes en utilisant des simulateurs quantiques.</p><p>Prix à gagner et opportunités de networking.</p>',
                'start_datetime' => Carbon::now()->addMonths(1)->startOfWeek()->addDays(4)->setHour(18)->setMinute(0), // Un vendredi soir
                'end_datetime' => Carbon::now()->addMonths(1)->startOfWeek()->addDays(6)->setHour(18)->setMinute(0), // Dimanche soir
                'location' => 'En ligne (Plateforme Discord & Simulateurs Cloud)',
                'is_featured' => true,
                'registration_url' => 'https://forms.example.com/hackathon-quantique',
                'target_audience' => 'Développeurs, Étudiants, Passionnés de quantique',
                'partners_to_attach' => $quantumLeap ? [$quantumLeap->id] : [],
            ],
            [
                'title' => 'École d\'Été sur la Théorie des Champs Quantiques',
                'description' => '<p>Une semaine intensive de cours et de travaux dirigés pour les étudiants en doctorat et les jeunes chercheurs souhaitant approfondir leurs connaissances en TQC.</p>',
                'start_datetime' => Carbon::now()->addMonths(4)->setHour(9)->setMinute(0),
                'end_datetime' => Carbon::now()->addMonths(4)->addDays(4)->setHour(17)->setMinute(0), // 5 jours
                'location' => 'Département de Physique - USTA',
                'is_featured' => false,
                'target_audience' => 'Doctorants, Post-doctorants en physique théorique',
            ],
        ];

        foreach ($events as $eventData) {
            $partnersToAttach = $eventData['partners_to_attach'] ?? [];
            unset($eventData['partners_to_attach']); // Retirer avant de passer à la factory

            $eventData['slug'] = Str::slug($eventData['title']);
            $eventData['meta_title'] = Str::limit($eventData['title'], 60);
            $eventData['meta_description'] = Str::limit(strip_tags($eventData['description']), 155);

            $event = Event::factory()->create($eventData);
            if (!empty($partnersToAttach) && Partner::count() > 0) {
                $event->partners()->sync($partnersToAttach);
            }
            $this->command->line("Created event: {$event->title}");
        }

        // Créer quelques événements supplémentaires avec la factory
        // La factory elle-même peut lier des partenaires aléatoires
        $this->command->info('Creating 6 additional random events...');
        Event::factory()->count(6)->create();
        $this->command->line("Created 6 additional random events.");

        $this->command->info('Events seeding completed.');
    }
}