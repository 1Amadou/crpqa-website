<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Events...');

        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $primaryLocale = config('app.locale', 'fr');

        if (User::count() == 0) {
            $this->command->warn('No users found. Creating a default user for events.');
            User::factory()->create(['name' => 'Default Event Creator', 'email' => 'eventcreator@example.com']);
        }
         if (Partner::count() == 0) {
            $this->command->warn('No partners found. Consider seeding partners for better event data.');
            // Optionnel: créer un partenaire par défaut ici si nécessaire
            // Partner::factory()->create(['name_fr' => 'Partenaire Principal', 'name_en' => 'Main Partner', 'slug' => 'main-partner']);
        }


        $adminOrEditor = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Super Administrateur', 'Éditeur']))
                             ->inRandomOrder()->first() ?? User::firstOrFail();
        
        $partners = Partner::all();

        $specificEventsData = [
            [
                'title_fr' => 'Conférence Internationale sur l\'IA et la Physique Quantique (FR)',
                'title_en' => 'International Conference on AI and Quantum Physics (EN)',
                'description_fr' => '<p>Rejoignez-nous pour une conférence de trois jours explorant les synergies entre l\'intelligence artificielle et les dernières avancées en physique quantique. Des orateurs de renommée mondiale partageront leurs perspectives. (FR)</p>',
                'description_en' => '<p>Join us for a three-day conference exploring the synergies between artificial intelligence and the latest breakthroughs in quantum physics. World-renowned speakers will share their insights. (EN)</p>',
                'location_fr' => 'Palais des Congrès, Bamako, Mali (FR)',
                'location_en' => 'Congress Palace, Bamako, Mali (EN)',
                'target_audience_fr' => 'Chercheurs, étudiants avancés, professionnels de l\'IA et de la physique. (FR)',
                'target_audience_en' => 'Researchers, advanced students, AI and physics professionals. (EN)',
                'meta_title_fr' => 'Conférence IA & Quantique 2025 - CRPQA (FR)',
                'meta_title_en' => 'AI & Quantum Conference 2025 - CRPQA (EN)',
                'meta_description_fr' => 'Participez à la conférence internationale sur l\'IA et la Physique Quantique organisée par le CRPQA. (FR)',
                'meta_description_en' => 'Attend the international conference on AI and Quantum Physics hosted by CRPQA. (EN)',
                'cover_image_alt_fr' => 'Logo de la conférence IA et Physique Quantique (FR)',
                'cover_image_alt_en' => 'AI and Quantum Physics conference logo (EN)',
                'start_datetime' => Carbon::now()->addMonths(2)->setHour(9)->setMinute(0),
                'end_datetime' => Carbon::now()->addMonths(2)->addDays(2)->setHour(17)->setMinute(0),
                'registration_url' => 'https://example.com/conference-ia-quantique/inscription',
                'is_featured' => true,
                'created_by_user_id' => $adminOrEditor->id,
                'partner_ids' => $partners->isNotEmpty() ? $partners->random(min(2, $partners->count()))->pluck('id')->all() : [],
            ],
            [
                'title_fr' => 'Atelier Pratique : Introduction à la Programmation Quantique (FR)',
                'title_en' => 'Hands-on Workshop: Introduction to Quantum Programming (EN)',
                'description_fr' => '<p>Un atelier intensif d\'une journée pour apprendre les bases de la programmation quantique avec Qiskit. Aucun prérequis en physique quantique n\'est nécessaire, mais des bases en Python sont recommandées. (FR)</p>',
                'description_en' => '<p>A one-day intensive workshop to learn the basics of quantum programming with Qiskit. No prior quantum physics knowledge required, but Python basics are recommended. (EN)</p>',
                'location_fr' => 'Salle de Formation CRPQA, Bamako (FR)',
                'location_en' => 'CRPQA Training Room, Bamako (EN)',
                'target_audience_fr' => 'Développeurs, étudiants en informatique, curieux de la tech. (FR)',
                'target_audience_en' => 'Developers, computer science students, tech enthusiasts. (EN)',
                'start_datetime' => Carbon::now()->addMonth()->startOfWeek()->addDays(4)->setHour(10)->setMinute(0), // Prochain vendredi
                'end_datetime' => Carbon::now()->addMonth()->startOfWeek()->addDays(4)->setHour(16)->setMinute(0),
                'created_by_user_id' => $adminOrEditor->id,
                'partner_ids' => $partners->isNotEmpty() ? $partners->random(1)->pluck('id')->all() : [],
            ]
        ];

        foreach ($specificEventsData as $data) {
            $partnerIdsToAttach = $data['partner_ids'] ?? [];
            unset($data['partner_ids']);

            if (empty($data['slug'])) {
                $titleForSlug = $data['title_' . $primaryLocale] ?? $data['title_en'] ?? 'evenement';
                $slug = Str::slug($titleForSlug);
                $originalSlug = $slug;
                $count = 1;
                while (Event::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $data['slug'] = $slug;
            }
            
            $event = Event::create($data); // Utiliser create()
            
            if (!empty($partnerIdsToAttach)) {
                $event->partners()->sync($partnerIdsToAttach);
            }
            $this->command->line("Created specific event: " . $event->getTranslation('title', $primaryLocale, false));
        }

        $numberOfRandomEvents = 8; // Créer moins d'événements aléatoires que pour les news/pubs
        $this->command->info("Creating {$numberOfRandomEvents} additional random events using factory...");
        Event::factory()->count($numberOfRandomEvents)->create();
        $this->command->line("Created {$numberOfRandomEvents} additional random events.");

        $this->command->info('Events seeding completed.');
    }
}