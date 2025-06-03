<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Champs à localiser et leurs types originaux approximatifs
        // (basé sur create_events_table, add_seo_fields_to_events_table, add_target_audience_to_events_table)
        $fieldsToLocalize = [
            'title'             => 'string',   // De create_events_table
            'description'       => 'longText', // De create_events_table
            'location'          => 'string',   // De create_events_table
            'meta_title'        => 'string',   // De add_seo_fields_to_events_table
            'meta_description'  => 'text',     // De add_seo_fields_to_events_table
            'target_audience'   => 'text',     // De add_target_audience_to_events_table
            'cover_image_alt'   => 'string',   // Nouveau champ pour l'alternative de l'image de couverture
        ];

        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $defaultLocale = config('app.locale', 'fr');

        Schema::table('events', function (Blueprint $table) use ($fieldsToLocalize, $availableLocales) {
            // 1. Ajouter les nouvelles colonnes localisées si elles n'existent pas
            foreach ($fieldsToLocalize as $fieldName => $originalType) {
                foreach ($availableLocales as $locale) {
                    $newColumnName = $fieldName . '_' . $locale;
                    if (!Schema::hasColumn('events', $newColumnName)) {
                        if ($originalType === 'longText') {
                            $table->longText($newColumnName)->nullable();
                        } elseif ($originalType === 'text') {
                            $table->text($newColumnName)->nullable();
                        } else { // string
                            $table->string($newColumnName)->nullable();
                        }
                        // Optionnel: clause ->after() si l'ordre est important et la colonne de base existe encore
                        // if (Schema::hasColumn('events', $fieldName)) {
                        //     $table->{$originalType}($newColumnName)->nullable()->after($fieldName);
                        // }
                    }
                }
            }
        });

        // 2. Copier les données des anciennes colonnes vers les nouvelles colonnes pour la langue par défaut
        if (in_array($defaultLocale, $availableLocales)) {
            foreach ($fieldsToLocalize as $fieldName => $originalType) {
                // Ne pas essayer de copier 'cover_image_alt' car elle n'existait pas avant
                if ($fieldName === 'cover_image_alt') {
                    continue;
                }

                $newColumnNameDefaultLocale = $fieldName . '_' . $defaultLocale;
                if (Schema::hasColumn('events', $fieldName) && Schema::hasColumn('events', $newColumnNameDefaultLocale)) {
                    DB::table('events')->orderBy('id')->chunkById(100, function ($events) use ($fieldName, $newColumnNameDefaultLocale) {
                        foreach ($events as $event) {
                            if (isset($event->{$fieldName})) {
                                DB::table('events')
                                    ->where('id', $event->id)
                                    ->where(function ($query) use ($newColumnNameDefaultLocale) {
                                        $query->whereNull($newColumnNameDefaultLocale)->orWhere($newColumnNameDefaultLocale, '');
                                    })
                                    ->update([$newColumnNameDefaultLocale => $event->{$fieldName}]);
                            }
                        }
                    });
                }
            }
        }

        Schema::table('events', function (Blueprint $table) {
            // 3. Supprimer les anciennes colonnes non localisées (sauf cover_image_alt qui est nouveau)
            $oldColumnsToDrop = ['title', 'description', 'location', 'meta_title', 'meta_description', 'target_audience'];
            foreach ($oldColumnsToDrop as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }

            // 4. Supprimer l'ancienne colonne cover_image_path (gérée par Spatie Media Library)
            if (Schema::hasColumn('events', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $defaultLocale = config('app.locale', 'fr');

            // Champs à restaurer avec leurs types originaux
            $fieldsToRestore = [
                'title'             => 'string',
                'description'       => 'longText',
                'location'          => 'string',
                'meta_title'        => 'string',
                'meta_description'  => 'text',
                'target_audience'   => 'text',
                'cover_image_path'  => 'string', // cover_image_path existait
            ];

            // 1. Recréer les anciennes colonnes non localisées
            // L'ordre avec ->after() peut être important pour matcher l'ancien schéma
            if (!Schema::hasColumn('events', 'title') && Schema::hasColumn('events', 'slug')) {
                $table->string('title')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('events', 'description') && Schema::hasColumn('events', 'title')) { // Exemple de positionnement
                $table->longText('description')->nullable()->after('title'); // Description était NOT NULL
            }
            if (!Schema::hasColumn('events', 'location') && Schema::hasColumn('events', 'end_datetime')) {
                $table->string('location')->nullable()->after('end_datetime');
            }
            if (!Schema::hasColumn('events', 'cover_image_path') && Schema::hasColumn('events', 'location')) {
                $table->string('cover_image_path')->nullable()->after('location');
            }
            if (!Schema::hasColumn('events', 'meta_title') && Schema::hasColumn('events', 'registration_url')) { // Approx.
                $table->string('meta_title')->nullable()->after('registration_url');
            }
            if (!Schema::hasColumn('events', 'meta_description') && Schema::hasColumn('events', 'meta_title')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
             if (!Schema::hasColumn('events', 'target_audience') && Schema::hasColumn('events', 'meta_description')) { // Approx.
                $table->text('target_audience')->nullable()->after('meta_description');
            }


            // 2. Copier les données de la langue par défaut vers les anciennes colonnes (optionnel)
            if (in_array($defaultLocale, $availableLocales)) {
                $fieldsToCopyBack = ['title', 'description', 'location', 'meta_title', 'meta_description', 'target_audience'];
                foreach ($fieldsToCopyBack as $fieldName) {
                    $localizedColumnName = $fieldName . '_' . $defaultLocale;
                    if (Schema::hasColumn('events', $localizedColumnName) && Schema::hasColumn('events', $fieldName)) {
                        DB::table('events')->orderBy('id')->chunkById(100, function ($events) use ($fieldName, $localizedColumnName) {
                            foreach ($events as $event) {
                                if (isset($event->{$localizedColumnName})) {
                                     DB::table('events')
                                        ->where('id', $event->id)
                                        ->update([$fieldName => $event->{$localizedColumnName}]);
                                }
                            }
                        });
                    }
                }
            }

            // 3. Supprimer les nouvelles colonnes localisées
            $fieldsToDropLocalized = ['title', 'description', 'location', 'meta_title', 'meta_description', 'target_audience', 'cover_image_alt'];
            foreach ($fieldsToDropLocalized as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnName = $fieldName . '_' . $locale;
                    if (Schema::hasColumn('events', $columnName)) {
                        $table->dropColumn($columnName);
                    }
                }
            }
        });
    }
};