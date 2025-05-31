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
        Schema::table('news', function (Blueprint $table) {
            $locales = config('app.available_locales', ['fr', 'en']);
        
            // Titre
            foreach ($locales as $locale) {
                if (!Schema::hasColumn('news', 'title_' . $locale)) {
                    $table->string('title_' . $locale)->nullable();
                }
            }
            // Summary
            foreach ($locales as $locale) {
                if (!Schema::hasColumn('news', 'summary_' . $locale)) {
                    $table->text('summary_' . $locale)->nullable();
                }
            }
            // Content
            foreach ($locales as $locale) {
                if (!Schema::hasColumn('news', 'content_' . $locale)) {
                    $table->longText('content_' . $locale)->nullable();
                }
            }
            // Meta Title
            foreach ($locales as $locale) {
                if (!Schema::hasColumn('news', 'meta_title_' . $locale)) {
                    $table->string('meta_title_' . $locale)->nullable();
                }
            }
            // Meta Description
            foreach ($locales as $locale) {
                if (!Schema::hasColumn('news', 'meta_description_' . $locale)) {
                    $table->text('meta_description_' . $locale)->nullable();
                }
            }
            // Cover Image Alt
            foreach ($locales as $locale) {
                if (!Schema::hasColumn('news', 'cover_image_alt_' . $locale)) {
                    $table->string('cover_image_alt_' . $locale)->nullable();
                }
            }
        });

        // 2. Copier les données existantes des anciennes colonnes vers la colonne de la langue par défaut
        $defaultLocaleForCopy = config('app.locale', 'fr'); //
        $baseTextFieldsToCopy = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];

        if (in_array($defaultLocaleForCopy, config('app.available_locales', ['fr', 'en']))) {
            foreach ($baseTextFieldsToCopy as $field) {
                $localizedColumnName = $field . '_' . $defaultLocaleForCopy;
                if (Schema::hasColumn('news', $field) && Schema::hasColumn('news', $localizedColumnName)) {
                    DB::table('news')->orderBy('id')->chunkById(100, function ($newsItems) use ($field, $localizedColumnName) {
                        foreach ($newsItems as $item) {
                            if (isset($item->{$field})) {
                                DB::table('news')
                                    ->where('id', $item->id)
                                    // Copier seulement si la colonne localisée est vide ou nulle
                                    ->where(function ($query) use ($localizedColumnName) {
                                        $query->whereNull($localizedColumnName)->orWhere($localizedColumnName, '');
                                    })
                                    ->update([$localizedColumnName => $item->{$field}]);
                            }
                        }
                    });
                }
            }
        }

        Schema::table('news', function (Blueprint $table) {
            // 3. Supprimer les anciennes colonnes de base non localisées (CONDITIONNELLEMENT)
            $baseColumnsToDrop = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];
            foreach ($baseColumnsToDrop as $field) {
                if (Schema::hasColumn('news', $field)) {
                    $table->dropColumn($field);
                }
            }
            
            // 4. Supprimer l'ancienne colonne 'cover_image_path' (gérée maintenant par Spatie Media Library)
            if (Schema::hasColumn('news', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
            // cover_image_url n'était pas dans create_news_table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $baseFieldsToRestore = [
                'title' => 'string',        // Type original de create_news_table
                'summary' => 'text',        // Type original de create_news_table
                'content' => 'longText',    // Type original de create_news_table
                'meta_title' => 'string',   // Type supposé de add_seo_fields_to_news_table
                'meta_description' => 'text', // Type supposé de add_seo_fields_to_news_table
                'cover_image_alt' => 'string', // Type supposé de add_is_published_and_image_alt_to_news_table
            ];
            $availableLocales = config('app.available_locales', ['fr', 'en']); //
            $defaultLocale = config('app.locale', 'fr'); //

            // 1. Recréer les anciennes colonnes de base si elles n'existent pas
            // (elles ont été supprimées par up())
            // L'ordre de recréation avec after() est important pour respecter le schéma original.
            if (!Schema::hasColumn('news', 'title') && Schema::hasColumn('news', 'slug')) {
                $table->string('title')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('news', 'summary') && Schema::hasColumn('news', 'title')) { // Ou une autre colonne de référence
                $table->text('summary')->nullable()->after('title'); // Approximatif
            }
            if (!Schema::hasColumn('news', 'content') && Schema::hasColumn('news', 'summary')) {
                $table->longText('content')->nullable()->after('summary'); // `content` était NOT NULL dans create_news_table
            }
            // Pour les champs SEO et cover_image_alt, il faut identifier leur position originale ou les ajouter.
            // Exemple simple:
            if (!Schema::hasColumn('news', 'meta_title')) {
                 $table->string('meta_title')->nullable(); // Placer après le contenu par exemple
            }
            if (!Schema::hasColumn('news', 'meta_description')) {
                 $table->text('meta_description')->nullable();
            }
            if (!Schema::hasColumn('news', 'cover_image_alt')) {
                 $table->string('cover_image_alt')->nullable();
            }

            // Recréer 'cover_image_path' (elle existait dans create_news_table)
            if (!Schema::hasColumn('news', 'cover_image_path') && Schema::hasColumn('news', 'content')) { // Placer après content
                $table->string('cover_image_path')->nullable()->after('content');
            }
        });

        // 2. Copier les données des champs localisés par défaut vers les anciennes colonnes (optionnel)
        $baseFieldsToRestoreData = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];
        $defaultLocale = config('app.locale', 'fr'); //

        if (in_array($defaultLocale, config('app.available_locales', ['fr', 'en']))) {
            foreach ($baseFieldsToRestoreData as $field) {
                $localizedColumnName = $field . '_' . $defaultLocale;
                if (Schema::hasColumn('news', $localizedColumnName) && Schema::hasColumn('news', $field)) {
                    DB::table('news')->orderBy('id')->chunkById(100, function ($newsItems) use ($field, $localizedColumnName) {
                        foreach ($newsItems as $item) {
                            if (isset($item->{$localizedColumnName})) {
                                DB::table('news')
                                    ->where('id', $item->id)
                                    ->update([$field => $item->{$localizedColumnName}]);
                            }
                        }
                    });
                }
            }
        }
        
        Schema::table('news', function (Blueprint $table) {
            // 3. Supprimer les colonnes localisées (celles ajoutées par up())
            $baseTextFieldsForDrop = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];
            $availableLocalesForDrop = config('app.available_locales', ['fr', 'en']); //

            foreach ($baseTextFieldsForDrop as $field) {
                foreach ($availableLocalesForDrop as $locale) {
                    $columnName = $field . '_' . $locale;
                    if (Schema::hasColumn('news', $columnName)) {
                        $table->dropColumn($columnName);
                    }
                }
            }
        });
    }
};