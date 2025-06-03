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
        // Champs à localiser et leurs types originaux d'après create_static_pages_table
        $fieldsToLocalize = [
            'title' => 'string',
            'content' => 'longText',
            'meta_title' => 'string',
            'meta_description' => 'text',
            'cover_image_alt_text' => 'string', // Nouveau champ pour l'alt de l'image
        ];

        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $defaultLocale = config('app.locale', 'fr');

        Schema::table('static_pages', function (Blueprint $table) use ($fieldsToLocalize, $availableLocales) {
            // 1. Ajouter les nouvelles colonnes localisées si elles n'existent pas
            foreach ($fieldsToLocalize as $fieldName => $originalType) {
                foreach ($availableLocales as $locale) {
                    $newColumnName = $fieldName . '_' . $locale;
                    if (!Schema::hasColumn('static_pages', $newColumnName)) {
                        if ($originalType === 'longText') {
                            $table->longText($newColumnName)->nullable();
                        } elseif ($originalType === 'text') {
                            $table->text($newColumnName)->nullable();
                        } else { // string
                            $table->string($newColumnName)->nullable();
                        }
                        // Optionnel: ->after() pour l'ordre, si la colonne de base $fieldName existe encore
                        // if (Schema::hasColumn('static_pages', $fieldName)) {
                        //     $table->string($newColumnName)->nullable()->after($fieldName); // S'assurer que le type est correct pour after()
                        // }
                    }
                }
            }
        });

        // 2. Copier les données des anciennes colonnes vers les nouvelles pour la langue par défaut
        if (in_array($defaultLocale, $availableLocales)) {
            // Ne copier que les champs qui existaient avant en version non localisée
            $fieldsToCopyData = ['title', 'content', 'meta_title', 'meta_description'];
            foreach ($fieldsToCopyData as $fieldName) {
                $newColumnNameDefaultLocale = $fieldName . '_' . $defaultLocale;
                if (Schema::hasColumn('static_pages', $fieldName) && Schema::hasColumn('static_pages', $newColumnNameDefaultLocale)) {
                    DB::table('static_pages')->orderBy('id')->chunkById(100, function ($pages) use ($fieldName, $newColumnNameDefaultLocale) {
                        foreach ($pages as $page) {
                            if (isset($page->{$fieldName})) {
                                DB::table('static_pages')
                                    ->where('id', $page->id)
                                    ->where(function ($query) use ($newColumnNameDefaultLocale) {
                                        $query->whereNull($newColumnNameDefaultLocale)->orWhere($newColumnNameDefaultLocale, '');
                                    })
                                    ->update([$newColumnNameDefaultLocale => $page->{$fieldName}]);
                            }
                        }
                    });
                }
            }
        }

        Schema::table('static_pages', function (Blueprint $table) {
            // 3. Supprimer les anciennes colonnes non localisées
            $oldColumnsToDrop = ['title', 'content', 'meta_title', 'meta_description'];
            foreach ($oldColumnsToDrop as $column) {
                if (Schema::hasColumn('static_pages', $column)) {
                    $table->dropColumn($column);
                }
            }
            // Note: cover_image_path n'existait pas dans create_static_pages_table, donc pas besoin de le supprimer ici.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $defaultLocale = config('app.locale', 'fr');

            $fieldsToRestore = [
                'title' => 'string',
                'content' => 'longText', // Était NOT NULL
                'meta_title' => 'string',
                'meta_description' => 'text',
            ];

            // 1. Recréer les anciennes colonnes non localisées
            if (!Schema::hasColumn('static_pages', 'title') && Schema::hasColumn('static_pages', 'slug')) {
                $table->string('title')->nullable()->after('slug'); // Était NOT NULL
            }
            if (!Schema::hasColumn('static_pages', 'content') && Schema::hasColumn('static_pages', 'title')) { // Ou slug si title a été recréé après
                $table->longText('content')->nullable()->after(Schema::hasColumn('static_pages', 'title') ? 'title' : 'slug'); // Était NOT NULL
            }
            if (!Schema::hasColumn('static_pages', 'meta_title') && Schema::hasColumn('static_pages', 'content')) {
                $table->string('meta_title')->nullable()->after('content');
            }
            if (!Schema::hasColumn('static_pages', 'meta_description') && Schema::hasColumn('static_pages', 'meta_title')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }

            // 2. Copier les données de la langue par défaut vers les anciennes colonnes
            if (in_array($defaultLocale, $availableLocales)) {
                $fieldsToCopyBack = ['title', 'content', 'meta_title', 'meta_description'];
                foreach ($fieldsToCopyBack as $fieldName) {
                    $localizedColumnName = $fieldName . '_' . $defaultLocale;
                    if (Schema::hasColumn('static_pages', $localizedColumnName) && Schema::hasColumn('static_pages', $fieldName)) {
                        DB::table('static_pages')->orderBy('id')->chunkById(100, function ($pages) use ($fieldName, $localizedColumnName) {
                            foreach ($pages as $page) {
                                 if (isset($page->{$localizedColumnName})) {
                                     DB::table('static_pages')
                                        ->where('id', $page->id)
                                        ->update([$fieldName => $page->{$localizedColumnName}]);
                                 }
                            }
                        });
                    }
                }
            }

            // 3. Supprimer les nouvelles colonnes localisées
            $fieldsToDropLocalized = ['title', 'content', 'meta_title', 'meta_description', 'cover_image_alt_text'];
            foreach ($fieldsToDropLocalized as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnName = $fieldName . '_' . $locale;
                    if (Schema::hasColumn('static_pages', $columnName)) {
                        $table->dropColumn($columnName);
                    }
                }
            }
        });
    }
};