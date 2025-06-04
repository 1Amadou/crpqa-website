<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use Illuminate\Support\Str;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Site Settings...');

        $defaultLocale = config('app.locale', 'fr');
        $availableLocales = config('app.available_locales', ['fr', 'en']);

        $settingsData = [
            'id' => 1,

            // --- Informations Générales et SEO du Site ---
            'site_name_fr' => 'CRPQA - Centre de Recherche en Physique Quantique et Applications',
            'site_name_en' => 'CRPQA - Research Center for Quantum Physics and Applications',
            'site_name_short_fr' => 'CRPQA',
            'site_name_short_en' => 'CRPQA',
            'site_description_fr' => 'Site officiel du Centre de Recherche en Physique Quantique et ses Applications. Explorez nos recherches, publications, événements et collaborations.',
            'site_description_en' => 'Official website of the Research Center for Quantum Physics and its Applications. Explore our research, publications, events, and collaborations.',
            'copyright_text_fr' => '© ' . date('Y') . ' CRPQA. Tous droits réservés.',
            'copyright_text_en' => '© ' . date('Y') . ' CRPQA. All rights reserved.',
            // 'seo_meta_title_fr' => 'Titre SEO Global FR', // Si différent du nom du site
            // 'seo_meta_title_en' => 'Global SEO Title EN',
            // 'seo_meta_description_fr' => 'Meta Description SEO Globale FR', // Si différent de site_description
            // 'seo_meta_description_en' => 'Global SEO Meta Description EN',

            // --- Section Héros (Page d'Accueil) ---
            'hero_main_title_fr' => 'L\'Avenir est',
            'hero_main_title_en' => 'The Future is',
            'hero_highlight_word_fr' => 'Quantique',
            'hero_highlight_word_en' => 'Quantum',
            'hero_subtitle_line2_fr' => 'Façonnez-le avec le CRPQA.',
            'hero_subtitle_line2_en' => 'Shape it with CRPQA.',
            'hero_description_fr' => 'Au cœur de la révolution scientifique, le CRPQA est le fer de lance de la recherche en physique quantique au Mali, ouvrant la voie à des innovations qui transformeront notre monde.',
            'hero_description_en' => 'At the heart of the scientific revolution, CRPQA spearheads quantum physics research in Mali, paving the way for innovations that will transform our world.',
            'hero_button1_text_fr' => 'Découvrir nos Axes',
            'hero_button1_text_en' => 'Discover our Axes',
            'hero_button2_text_fr' => 'Nos Publications',
            'hero_button2_text_en' => 'Our Publications',
            'hero_banner_image_alt_fr' => 'Illustration de concepts de physique quantique pour la bannière du héros',
            'hero_banner_image_alt_en' => 'Illustration of quantum physics concepts for hero banner',
            'hero_button1_url' => '/domaines-recherche',
            'hero_button1_icon' => 'arrow-forward-outline',
            'hero_button2_url' => '/publications',
            'hero_button2_icon' => 'book-outline',

            // --- Section "À Propos" (Page d'Accueil) ---
            'about_home_title_fr' => 'Au Cœur de la Révolution Quantique',
            'about_home_title_en' => 'At the Heart of the Quantum Revolution',
            'about_home_subtitle_fr' => 'Notre Centre',
            'about_home_subtitle_en' => 'Our Center',
            'about_home_short_description_fr' => 'Fondé sur un héritage d\'excellence, le CRPQA se positionne comme un pôle majeur pour façonner l\'avenir de la physique quantique.',
            'about_home_short_description_en' => 'Building on a legacy of excellence, CRPQA stands as a major hub to shape the future of quantum physics.',
            'about_home_points' => json_encode([
                ['icon' => 'rocket-outline', 'text_fr' => 'Recherche de pointe et innovation continue', 'text_en' => 'Cutting-edge research and continuous innovation'],
                ['icon' => 'school-outline', 'text_fr' => 'Formation d\'excellence pour les futurs leaders', 'text_en' => 'Excellence in training for future leaders'],
                ['icon' => 'earth-outline', 'text_fr' => 'Collaborations et impact international', 'text_en' => 'International collaborations and impact'],
            ]),
            'about_page_slug' => 'a-propos', // Slug pour la page "À Propos" dédiée

            // --- Section CTA (Page d'Accueil) ---
            'home_cta_title_fr' => 'Façonnons l\'Avenir Ensemble',
            'home_cta_title_en' => 'Let\'s Shape the Future Together',
            'home_cta_text_fr' => 'Le CRPQA est ouvert aux collaborations avec des institutions, des entreprises et des chercheurs. Contactez-nous.',
            'home_cta_text_en' => 'CRPQA is open to collaborations with institutions, companies, and researchers. Contact us.',
            'home_cta_button1_text_fr' => 'Devenir Partenaire',
            'home_cta_button1_text_en' => 'Become a Partner',
            'home_cta_button2_text_fr' => 'Nous Rejoindre',
            'home_cta_button2_text_en' => 'Join Us',
            'home_cta_button1_url' => '/contact',
            'home_cta_button1_icon' => 'people-circle-outline',
            'home_cta_button2_url' => '/page/carrieres', // Slug de la page carrières
            'home_cta_button2_icon' => 'school-outline',
            'careers_page_slug' => 'carrieres',

            // --- Contenu spécifique à la Page "À Propos" Dédiée ---
            'about_page_hero_title_fr' => 'Découvrez le CRPQA',
            'about_page_hero_title_en' => 'Discover CRPQA',
            'about_page_hero_subtitle_fr' => 'Notre histoire, notre mission, notre vision pour l\'avenir de la physique quantique.',
            'about_page_hero_subtitle_en' => 'Our history, our mission, our vision for the future of quantum physics.',
            'about_introduction_title_fr' => 'Qui Sommes-Nous ?',
            'about_introduction_title_en' => 'Who We Are',
            'about_introduction_content_fr' => '<p>Le Centre de Recherche en Physique Quantique et ses Applications (CRPQA) est un établissement de premier plan dédié à l\'avancement des connaissances et des applications dans le domaine fascinant de la physique quantique...</p>',
            'about_introduction_content_en' => '<p>The Research Center for Quantum Physics and its Applications (CRPQA) is a leading institution dedicated to advancing knowledge and applications in the fascinating field of quantum physics...</p>',
            'about_history_title_fr' => 'Notre Histoire en Quelques Dates Clés',
            'about_history_title_en' => 'Our History in Key Dates',
            'about_history_timeline_json' => json_encode([
                ['year' => '2020', 'icon' => 'flag-outline', 'title_fr' => 'Création Initiale', 'title_en' => 'Initial Creation', 'description_fr' => 'Premiers jalons posés pour le centre.', 'description_en' => 'First milestones laid for the center.'],
                ['year' => '2023', 'icon' => 'bulb-outline', 'title_fr' => 'Lancement des Premiers Projets', 'title_en' => 'Launch of First Projects', 'description_fr' => 'Début des activités de recherche structurées.', 'description_en' => 'Start of structured research activities.'],
                ['year' => '2025', 'icon' => 'ribbon-outline', 'title_fr' => 'Inauguration Officielle', 'title_en' => 'Official Inauguration', 'description_fr' => 'Ouverture du centre au public et à la communauté scientifique.', 'description_en' => 'Opening of the center to the public and scientific community.'],
            ]),
            'about_mission_title_fr' => 'Notre Mission',
            'about_mission_title_en' => 'Our Mission',
            'about_mission_content_fr' => '<p>Promouvoir la recherche d\'excellence en physique quantique, former une nouvelle génération de scientifiques et développer des applications technologiques innovantes pour le Mali et l\'Afrique.</p>',
            'about_mission_content_en' => '<p>To promote excellence in quantum physics research, train a new generation of scientists, and develop innovative technological applications for Mali and Africa.</p>',
            'about_mission_icon_class' => 'rocket-outline',
            'about_vision_title_fr' => 'Notre Vision',
            'about_vision_title_en' => 'Our Vision',
            'about_vision_content_fr' => '<p>Devenir un centre de référence mondial en physique quantique, reconnu pour ses contributions scientifiques majeures et son rôle moteur dans le développement technologique durable.</p>',
            'about_vision_content_en' => '<p>To become a world-renowned center in quantum physics, recognized for its major scientific contributions and its driving role in sustainable technological development.</p>',
            'about_vision_icon_class' => 'eye-outline',
            'about_values_title_fr' => 'Nos Valeurs Fondamentales',
            'about_values_title_en' => 'Our Core Values',
            'about_values_list_json' => json_encode([
                ['text_fr' => 'Excellence Scientifique', 'text_en' => 'Scientific Excellence'],
                ['text_fr' => 'Innovation Continue', 'text_en' => 'Continuous Innovation'],
                ['text_fr' => 'Collaboration Ouverte', 'text_en' => 'Open Collaboration'],
                ['text_fr' => 'Intégrité et Éthique', 'text_en' => 'Integrity and Ethics'],
                ['text_fr' => 'Impact Sociétal', 'text_en' => 'Societal Impact'],
            ]),
            'about_values_icon_class' => 'diamond-outline',
            'about_director_message_title_fr' => 'Message du Directeur',
            'about_director_message_title_en' => 'Director\'s Message',
            'about_director_name_fr' => 'Prof. Nom du Directeur/Directrice',
            'about_director_name_en' => 'Prof. Director\'s Name',
            'about_director_position_fr' => 'Directeur Général du CRPQA',
            'about_director_position_en' => 'Director General of CRPQA',
            'about_director_message_content_fr' => '<p>Un mot de bienvenue et une présentation de la vision stratégique du centre...</p>',
            'about_director_message_content_en' => '<p>A welcome message and presentation of the center\'s strategic vision...</p>',
            'about_decree_title_fr' => 'Décret de Création',
            'about_decree_title_en' => 'Founding Decree',
            'about_decree_intro_text_fr' => '<p>Le CRPQA a été établi par le décret N°XXXX du JJ/MM/AAAA, marquant l\'engagement de l\'État Malien...</p>',
            'about_decree_intro_text_en' => '<p>CRPQA was established by Decree No. XXXX of DD/MM/YYYY, marking the Malian State\'s commitment...</p>',
            'about_fst_title_fr' => 'Notre Ancrage : La FST (USTTB)',
            'about_fst_title_en' => 'Our Anchor: The FST (USTTB)',
            'about_fst_content_fr' => '<p>Le CRPQA est fier d\'être hébergé au sein de la Faculté des Sciences et Techniques de l\'Université des Sciences, des Techniques et des Technologies de Bamako...</p>',
            'about_fst_content_en' => '<p>CRPQA is proud to be hosted within the Faculty of Sciences and Techniques of the University of Sciences, Techniques and Technologies of Bamako...</p>',
            'about_fst_statistics_json' => json_encode([
                ['label_fr' => 'Départements à la FST', 'label_en' => 'Departments at FST', 'value' => 'XX+'],
                ['label_fr' => 'Étudiants en Physique', 'label_en' => 'Physics Students', 'value' => 'YYY+'],
                ['label_fr' => 'Laboratoires de Recherche', 'label_en' => 'Research Laboratories', 'value' => 'ZZ+'],
            ]),

            // --- Contacts & Sociaux (Non Traduits) ---
            'contact_email' => 'info@crpqa.ml',
            'contact_phone' => '+223 20 00 00 00',
            'maps_url' => 'https://maps.google.com/?q=USTTB+Bamako',
            'facebook_url' => 'https://facebook.com/crpqamali',
            'twitter_url' => 'https://twitter.com/crpqamali',
            'linkedin_url' => 'https://linkedin.com/company/crpqamali',
            'youtube_url' => 'https://youtube.com/crpqamali',
            // 'instagram_url' => null,

            // --- Conformité et Système ---
            'cookie_consent_enabled' => true,
            'maintenance_mode' => false,
            'default_sender_email' => 'noreply@crpqa.ml',
            'default_sender_name' => 'CRPQA Mali',
            'google_analytics_id' => null, // 'G-XXXXXXXXXX'
        ];
        
        // S'assurer que tous les champs localisés attendus par le modèle ont une valeur pour chaque locale
        $finalSettingsData = $settingsData;
        $siteSettingModelInstance = new SiteSetting(); // Pour accéder à $localizedFields
        $localizedFieldsFromModel = $siteSettingModelInstance->localizedFields;

        foreach ($localizedFieldsFromModel as $baseFieldName) {
            foreach ($availableLocales as $locale) {
                $suffixedFieldName = $baseFieldName . '_' . $locale;
                if (!array_key_exists($suffixedFieldName, $finalSettingsData) || is_null($finalSettingsData[$suffixedFieldName])) {
                    // Tenter de prendre la valeur de la locale par défaut si non spécifiée pour cette locale
                    $defaultSuffixedFieldName = $baseFieldName . '_' . $defaultLocale;
                    if (array_key_exists($defaultSuffixedFieldName, $finalSettingsData)) {
                        $finalSettingsData[$suffixedFieldName] = $finalSettingsData[$defaultSuffixedFieldName] . ($locale !== $defaultLocale ? ' (' . strtoupper($locale) . ' - auto)' : '');
                    } else {
                         $finalSettingsData[$suffixedFieldName] = Str::title(str_replace('_', ' ', $baseFieldName)) . ' (' . strtoupper($locale) . ' - placeholder)';
                    }
                }
            }
        }

        // Utiliser updateOrCreate pour s'assurer que l'enregistrement avec ID 1 est créé ou mis à jour
        SiteSetting::updateOrCreate(
            ['id' => 1],
            $finalSettingsData // Utiliser le tableau complété
        );

        $this->command->info('Site Settings seeded/updated successfully with "About Page" content.');
    }
}