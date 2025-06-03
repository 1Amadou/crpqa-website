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
        $fieldsToLocalize = [
            'name' => 'string',
            'description' => 'text',
            'logo_alt_text' => 'string', // Nouveau champ pour l'alternative du logo
        ];

        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $defaultLocale = config('app.locale', 'fr');

        Schema::table('partners', function (Blueprint $table) use ($fieldsToLocalize, $availableLocales) {
            // 1. Ajouter les nouvelles colonnes localisées si elles n'existent pas
            foreach ($fieldsToLocalize as $fieldName => $originalType) {
                foreach ($availableLocales as $locale) {
                    $newColumnName = $fieldName . '_' . $locale;
                    if (!Schema::hasColumn('partners', $newColumnName)) {
                        if ($originalType === 'text' || $originalType === 'longText') {
                            $table->text($newColumnName)->nullable();
                        } else { // string
                            $table->string($newColumnName)->nullable();
                        }
                        // Optionnel: clause ->after() si l'ordre est important
                        // Exemple pour name, si l'ancienne colonne 'name' existe encore:
                        // if ($fieldName === 'name' && Schema::hasColumn('partners', 'name')) {
                        //     $table->string($newColumnName)->nullable()->after('name');
                        // }
                    }
                }
            }
        });

        // 2. Copier les données des anciennes colonnes vers les nouvelles colonnes pour la langue par défaut
        if (in_array($defaultLocale, $availableLocales)) {
            $fieldsToCopyData = ['name', 'description']; // Ne pas copier logo_alt_text car il est nouveau
            foreach ($fieldsToCopyData as $fieldName) {
                $newColumnNameDefaultLocale = $fieldName . '_' . $defaultLocale;
                if (Schema::hasColumn('partners', $fieldName) && Schema::hasColumn('partners', $newColumnNameDefaultLocale)) {
                    DB::table('partners')->orderBy('id')->chunkById(100, function ($partners) use ($fieldName, $newColumnNameDefaultLocale) {
                        foreach ($partners as $partner) {
                            if (isset($partner->{$fieldName})) {
                                DB::table('partners')
                                    ->where('id', $partner->id)
                                    ->where(function ($query) use ($newColumnNameDefaultLocale) {
                                        $query->whereNull($newColumnNameDefaultLocale)->orWhere($newColumnNameDefaultLocale, '');
                                    })
                                    ->update([$newColumnNameDefaultLocale => $partner->{$fieldName}]);
                            }
                        }
                    });
                }
            }
        }

        Schema::table('partners', function (Blueprint $table) {
            // 3. Supprimer les anciennes colonnes non localisées
            $oldColumnsToDrop = ['name', 'description'];
            foreach ($oldColumnsToDrop as $column) {
                if (Schema::hasColumn('partners', $column)) {
                    $table->dropColumn($column);
                }
            }

            // 4. Supprimer l'ancienne colonne logo_path (gérée par Spatie Media Library)
            if (Schema::hasColumn('partners', 'logo_path')) {
                $table->dropColumn('logo_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $defaultLocale = config('app.locale', 'fr');

            // Champs à restaurer avec leurs types originaux
            $fieldsToRestore = [
                'name' => 'string',
                'description' => 'text',
                'logo_path' => 'string', // logo_path existait
            ];

            // 1. Recréer les anciennes colonnes non localisées
            // L'ordre avec ->after() peut être important pour matcher l'ancien schéma
            if (!Schema::hasColumn('partners', 'name') && Schema::hasColumn('partners', 'id')) { // après id par défaut
                $table->string('name')->nullable()->after('id'); // 'name' était NOT NULL dans la migration originale. Adapter si besoin.
            }
            if (!Schema::hasColumn('partners', 'logo_path') && Schema::hasColumn('partners', 'name')) {
                $table->string('logo_path')->nullable()->after('name');
            }
            if (!Schema::hasColumn('partners', 'description') && Schema::hasColumn('partners', 'website_url')) { // website_url est après logo_path
                $table->text('description')->nullable()->after('website_url');
            }


            // 2. Copier les données de la langue par défaut vers les anciennes colonnes
            if (in_array($defaultLocale, $availableLocales)) {
                $fieldsToCopyBack = ['name', 'description'];
                foreach ($fieldsToCopyBack as $fieldName) {
                    $localizedColumnName = $fieldName . '_' . $defaultLocale;
                    if (Schema::hasColumn('partners', $localizedColumnName) && Schema::hasColumn('partners', $fieldName)) {
                         DB::table('partners')->orderBy('id')->chunkById(100, function ($partners) use ($fieldName, $localizedColumnName) {
                            foreach ($partners as $partner) {
                                if (isset($partner->{$localizedColumnName})) {
                                    DB::table('partners')
                                        ->where('id', $partner->id)
                                        ->update([$fieldName => $partner->{$localizedColumnName}]);
                                }
                            }
                        });
                    }
                }
            }

            // 3. Supprimer les nouvelles colonnes localisées
            $fieldsToDropLocalized = ['name', 'description', 'logo_alt_text'];
            foreach ($fieldsToDropLocalized as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnName = $fieldName . '_' . $locale;
                    if (Schema::hasColumn('partners', $columnName)) {
                        $table->dropColumn($columnName);
                    }
                }
            }
        });
    }
};