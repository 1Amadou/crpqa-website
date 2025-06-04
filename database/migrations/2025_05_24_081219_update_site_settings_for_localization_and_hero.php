<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $defaultLocale = config('app.locale', 'fr');

        // Champs de base (non suffixés) qui existaient AVANT cette migration
        // et pour lesquels nous allons créer des versions localisées ET supprimer l'original.
        // Nous allons majoritairement utiliser TEXT pour la version localisée.
        $baseFieldsToLocalizeAndThenDrop = [
            'site_name'                 => ['type' => 'text'], // TEXT pour être sûr
            'address'                   => ['type' => 'text'],
            'footer_text'               => ['type' => 'text'],
            'cookie_consent_message'    => ['type' => 'text'],
            'maintenance_message'       => ['type' => 'text'],
            'hero_title'                => ['type' => 'text'], 
            'hero_subtitle'             => ['type' => 'text'], 
            'seo_meta_title'            => ['type' => 'text'], // TEXT pour être sûr
            'seo_meta_description'      => ['type' => 'text'],
        ];
        
        // Nouveaux champs purement localisés (pas de version non suffixée à supprimer)
        // Majoritairement TEXT pour éviter les problèmes de taille de ligne.
        $newLocalizedFields = [
            'site_name_short'                   => 'string', // Acronyme, peut rester string court
            'site_description'                  => 'text',
            'copyright_text'                    => 'text',
            'hero_main_title'                   => 'text',
            'hero_highlight_word'               => 'string', // Un mot ou deux, string court OK
            'hero_subtitle_line2'               => 'text',
            'hero_description'                  => 'text',
            'hero_button1_text'                 => 'string', // Texte de bouton, string court OK
            'hero_button2_text'                 => 'string',
            'hero_banner_image_alt'             => 'text',
            'about_home_title'                  => 'text',
            'about_home_subtitle'               => 'text',
            'about_home_short_description'      => 'text',
            'home_cta_title'                    => 'text',
            'home_cta_text'                     => 'text',
            'home_cta_button1_text'             => 'string',
            'home_cta_button2_text'             => 'string',
            'about_page_hero_title'             => 'text',
            'about_page_hero_subtitle'          => 'text',
            'about_introduction_title'          => 'text',
            'about_introduction_content'        => 'text',
            'about_history_title'               => 'text',
            'about_mission_title'               => 'text',
            'about_mission_content'             => 'text',
            'about_vision_title'                => 'text',
            'about_vision_content'              => 'text',
            'about_values_title'                => 'text',
            'about_director_message_title'      => 'text',
            'about_director_name'               => 'string', // Nom propre, string OK
            'about_director_position'           => 'text',
            'about_director_message_content'    => 'text',
            'about_decree_title'                => 'text',
            'about_decree_intro_text'           => 'text',
            'about_fst_title'                   => 'text',
            'about_fst_content'                 => 'text',
        ];

        // Nouveaux champs non traduits
        // Garder string pour les URLs, slugs, icon classes, GA ID, emails, noms courts, hex colors
        $newNonLocalizedFields = [
            'hero_button1_url' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'hero_button1_icon' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'hero_button2_url' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'hero_button2_icon' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'about_home_points' => ['type' => 'json', 'nullable' => true],
            'about_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'careers_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'home_cta_button1_url' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'home_cta_button1_icon' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'home_cta_button2_url' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'home_cta_button2_icon' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'cookie_policy_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'privacy_policy_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'terms_of_service_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'about_mission_icon_class' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'about_vision_icon_class' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'about_values_icon_class' => ['type' => 'string', 'nullable' => true, 'max' => 100],
            'about_history_timeline_json' => ['type' => 'json', 'nullable' => true],
            'about_values_list_json' => ['type' => 'json', 'nullable' => true],
            'about_fst_statistics_json' => ['type' => 'json', 'nullable' => true],
            'color_hex' => ['type' => 'string', 'nullable' => true, 'max' => 7], // Champ pour SiteSetting (pas ResearchAxis ici)
            // 'instagram_url' => ['type' => 'string', 'nullable' => true, 'max' => 255], // Si vous l'ajoutez
            'about_home_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'about_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'careers_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'cookie_policy_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'privacy_policy_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
            'terms_of_service_page_slug' => ['type' => 'string', 'nullable' => true, 'max' => 255],
        ];
        
        $oldImagePathsToDrop = ['logo_path', 'favicon_path', 'hero_bg_image_url', 'hero_banner_image_url', 'about_home_image_url', 'home_cta_bg_image_url'];

        Schema::table('site_settings', function (Blueprint $table) use ($baseFieldsToLocalizeAndThenDrop, $newLocalizedFields, $availableLocales, $newNonLocalizedFields) {
            $lastProcessedColumn = 'id'; 

            $allFieldsToLocalize = array_merge(array_keys($baseFieldsToLocalizeAndThenDrop), array_keys($newLocalizedFields));
            $allFieldTypes = array_merge($baseFieldsToLocalizeAndThenDrop, $newLocalizedFields);

            foreach ($allFieldsToLocalize as $fieldName) {
                $originalType = $allFieldTypes[$fieldName]['type'] ?? $allFieldTypes[$fieldName];
                foreach ($availableLocales as $locale) {
                    $newColumnName = $fieldName . '_' . $locale;
                    if (!Schema::hasColumn('site_settings', $newColumnName)) {
                        $columnDefinition = null;
                        if ($originalType === 'text' || $originalType === 'longText') {
                            $columnDefinition = $table->text($newColumnName)->nullable();
                        } else { // string (VARCHAR)
                            $maxLength = 255; // Longueur par défaut pour string
                            if ($fieldName === 'site_name_short' || str_ends_with($fieldName, '_icon') || $fieldName === 'hero_highlight_word' || str_ends_with($fieldName, '_button1_text') || str_ends_with($fieldName, '_button2_text')) {
                                $maxLength = 100; // Plus court pour certains strings
                            }
                            $columnDefinition = $table->string($newColumnName, $maxLength)->nullable();
                        }
                        
                        if (Schema::hasColumn('site_settings', $lastProcessedColumn)) {
                            $columnDefinition->after($lastProcessedColumn);
                        }
                    }
                    $lastProcessedColumn = $newColumnName;
                }
            }

            foreach ($newNonLocalizedFields as $fieldName => $details) {
                 if (!Schema::hasColumn('site_settings', $fieldName)) {
                    $columnType = $details['type'];
                    $length = ($columnType === 'string' && isset($details['max'])) ? $details['max'] : null;
                    $columnDefinition = $table->{$columnType}($fieldName, $length)->nullable($details['nullable'] ?? true);
                    
                    $afterRef = $details['after'] ?? $lastProcessedColumn; // 'after' est optionnel
                    if (Schema::hasColumn('site_settings', $afterRef)){
                        $columnDefinition->after($afterRef);
                    }
                    $lastProcessedColumn = $fieldName;
                }
            }
        });

        // Copie des données ...
        if (in_array($defaultLocale, $availableLocales)) {
            foreach ($baseFieldsToLocalizeAndThenDrop as $fieldName => $details) {
                $newColumnNameDefaultLocale = $fieldName . '_' . $defaultLocale;
                $sourceField = ($fieldName === 'site_description') ? 'seo_meta_description' : $fieldName;

                if (Schema::hasColumn('site_settings', $sourceField) && Schema::hasColumn('site_settings', $newColumnNameDefaultLocale)) {
                    DB::table('site_settings')->whereNotNull($sourceField)->chunkById(1, function ($settings) use ($sourceField, $newColumnNameDefaultLocale) {
                        foreach ($settings as $setting) {
                             DB::table('site_settings')->where('id', $setting->id)->update([$newColumnNameDefaultLocale => $setting->{$sourceField}]);
                        }
                    });
                }
            }
        }

        Schema::table('site_settings', function (Blueprint $table) use ($baseFieldsToLocalizeAndThenDrop, $oldImagePathsToDrop) {
            $columnsToEffectivelyDrop = [];
            foreach ($baseFieldsToLocalizeAndThenDrop as $fieldName => $details) {
                if (Schema::hasColumn('site_settings', $fieldName . '_' . config('app.locale', 'fr'))) {
                    if (Schema::hasColumn('site_settings', $fieldName)) {
                        $columnsToEffectivelyDrop[] = $fieldName;
                    }
                }
            }
            foreach ($oldImagePathsToDrop as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $columnsToEffectivelyDrop[] = $column;
                }
            }
            if (!empty($columnsToEffectivelyDrop)) {
                $table->dropColumn(array_unique($columnsToEffectivelyDrop));
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $defaultLocale = config('app.locale', 'fr');

            $baseFieldsToRestore = [
                'site_name'                 => ['type' => 'string'],
                'address'                   => ['type' => 'text'],
                'footer_text'               => ['type' => 'text'],
                'cookie_consent_message'    => ['type' => 'text'],
                'maintenance_message'       => ['type' => 'text'],
                'seo_meta_title'            => ['type' => 'string'],
                'seo_meta_description'      => ['type' => 'text'],
            ];

            $newLocalizedFieldsToDrop = [
                'site_name_short', 'site_description', 'copyright_text',
                'hero_main_title', 'hero_highlight_word', 'hero_subtitle_line2', 'hero_description',
                'hero_button1_text', 'hero_button2_text', 'hero_banner_image_alt',
                'about_home_title', 'about_home_subtitle', 'about_home_short_description',
                'home_cta_title', 'home_cta_text', 'home_cta_button1_text', 'home_cta_button2_text',
            ];

            $newNonLocalizedToDrop = [
                'hero_button1_url', 'hero_button1_icon', 'hero_button2_url', 'hero_button2_icon',
                'about_home_points', 'about_home_page_slug', 'about_page_slug', 'careers_page_slug', // <<---- AJOUTÉ ICI
                'home_cta_button1_url', 'home_cta_button1_icon', 'home_cta_button2_url', 'home_cta_button2_icon',
                'cookie_policy_page_slug', 'privacy_policy_page_slug', 'terms_of_service_page_slug',
            ];

            $oldImagePathsToRestore = [
                'logo_path' => 'string',
                'favicon_path' => 'string',
                'hero_bg_image_url' => 'string',
                'hero_banner_image_url' => 'string',
                'about_home_image_url' => 'string',
                'home_cta_bg_image_url' => 'string',
            ];

            // Supprimer les champs localisés de la page "À Propos"
            $aboutPageLocalizedToDrop = [
                'about_page_hero_title', 'about_page_hero_subtitle', 'about_introduction_title', 
                'about_introduction_content', 'about_history_title', 'about_mission_title', 
                'about_mission_content', 'about_vision_title', 'about_vision_content', 
                'about_values_title', 'about_director_message_title', 'about_director_name',
                'about_director_position', 'about_director_message_content', 'about_decree_title',
                'about_decree_intro_text', 'about_fst_title', 'about_fst_content',
            ];
            foreach ($aboutPageLocalizedToDrop as $fieldName) {
                foreach ($availableLocales as $locale) { // $availableLocales doit être défini
                    if (Schema::hasColumn('site_settings', $fieldName . '_' . $locale)) {
                    $table->dropColumn($fieldName . '_' . $locale);
                    }
                }
            }

            // Supprimer les champs non traduits de la page "À Propos"
            $aboutPageNonLocalizedToDrop = [
                'about_mission_icon_class', 'about_vision_icon_class', 'about_values_icon_class',
                'about_history_timeline_json', 'about_values_list_json', 'about_fst_statistics_json',
            ];
            foreach ($aboutPageNonLocalizedToDrop as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }

            $columnsToDropFromLocalized = [];
            foreach (array_keys($baseFieldsToRestore) as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnsToDropFromLocalized[] = $fieldName . '_' . $locale;
                }
            }
            foreach ($newLocalizedFieldsToDrop as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnsToDropFromLocalized[] = $fieldName . '_' . $locale;
                }
            }
            
            $existingColumnsToDrop = [];
            foreach (array_unique($columnsToDropFromLocalized) as $colName) {
                if (Schema::hasColumn('site_settings', $colName)) {
                    $existingColumnsToDrop[] = $colName;
                }
            }
            if (!empty($existingColumnsToDrop)) {
                $table->dropColumn($existingColumnsToDrop);
            }

            $existingNonLocalizedToDrop = [];
            foreach ($newNonLocalizedToDrop as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $existingNonLocalizedToDrop[] = $column;
                }
            }
            if(!empty($existingNonLocalizedToDrop)){
                $table->dropColumn($existingNonLocalizedToDrop);
            }

            foreach ($baseFieldsToRestore as $fieldName => $details) {
                if (!Schema::hasColumn('site_settings', $fieldName)) {
                    $table->{$details['type']}($fieldName)->nullable();
                }
            }

            foreach ($oldImagePathsToRestore as $fieldName => $type) {
                if (!Schema::hasColumn('site_settings', $fieldName)) {
                     $table->{$type}($fieldName)->nullable();
                }
            }
        });
    }
};