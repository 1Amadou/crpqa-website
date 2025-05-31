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
        Schema::table('publications', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']); //

            // 1. Ajouter les nouvelles colonnes localisées
            // Basé sur la structure originale : title était string, abstract était text.
            if (!Schema::hasColumn('publications', 'title_fr')) {
                // L'ancienne colonne 'title' était après 'slug'
                $table->string('title_fr')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('publications', 'title_en')) {
                $table->string('title_en')->nullable()->after('title_fr');
            }
            if (!Schema::hasColumn('publications', 'abstract_fr')) {
                // L'ancienne colonne 'abstract' était après 'authors_external'
                // Assurons-nous que 'title_en' existe avant de mettre 'abstract_fr' après
                if (Schema::hasColumn('publications', 'title_en')) {
                    $table->text('abstract_fr')->nullable()->after('title_en');
                } else { // Fallback si title_en n'a pas pu être ajouté pour une raison ou une autre
                    $table->text('abstract_fr')->nullable()->after('title_fr');
                }
            }
            if (!Schema::hasColumn('publications', 'abstract_en')) {
                $table->text('abstract_en')->nullable()->after('abstract_fr');
            }
        });

        // 2. Copier les données des anciennes colonnes vers les nouvelles colonnes pour la langue par défaut
        $defaultLocale = config('app.locale', 'fr'); // 'fr' d'après votre config/app.php

        // Copie pour 'title'
        if (Schema::hasColumn('publications', 'title') && Schema::hasColumn('publications', 'title_' . $defaultLocale)) {
            DB::table('publications')->orderBy('id')->chunkById(100, function ($publications) use ($defaultLocale) {
                foreach ($publications as $publication) {
                    if (isset($publication->title)) {
                        DB::table('publications')
                            ->where('id', $publication->id)
                            ->update(['title_' . $defaultLocale => $publication->title]);
                    }
                }
            });
        }

        // Copie pour 'abstract'
        if (Schema::hasColumn('publications', 'abstract') && Schema::hasColumn('publications', 'abstract_' . $defaultLocale)) {
            DB::table('publications')->orderBy('id')->chunkById(100, function ($publications) use ($defaultLocale) {
                foreach ($publications as $publication) {
                     if (isset($publication->abstract)) {
                        DB::table('publications')
                            ->where('id', $publication->id)
                            ->update(['abstract_' . $defaultLocale => $publication->abstract]);
                    }
                }
            });
        }

        // 3. Supprimer les anciennes colonnes non localisées
        Schema::table('publications', function (Blueprint $table) {
            if (Schema::hasColumn('publications', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('publications', 'abstract')) {
                $table->dropColumn('abstract');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']); //
            $defaultLocale = config('app.locale', 'fr'); //

            // 1. Recréer les anciennes colonnes non localisées avec leurs types originaux
            if (!Schema::hasColumn('publications', 'title')) {
                $table->string('title')->nullable()->after('slug'); // Positionnement d'après create_publications_table
            }
            if (!Schema::hasColumn('publications', 'abstract')) {
                if (Schema::hasColumn('publications', 'authors_external')) { // Positionnement d'après create_publications_table
                    $table->text('abstract')->nullable()->after('authors_external');
                } else {
                    $table->text('abstract')->nullable(); // Fallback
                }
            }
        });

        // 2. Copier les données des colonnes de la langue par défaut vers les anciennes colonnes (optionnel)
        // Copie pour 'title'
        if (Schema::hasColumn('publications', 'title_' . $defaultLocale) && Schema::hasColumn('publications', 'title')) {
            DB::table('publications')->orderBy('id')->chunkById(100, function ($publications) use ($defaultLocale) {
                foreach ($publications as $publication) {
                    if (isset($publication->{'title_' . $defaultLocale})) {
                        DB::table('publications')
                            ->where('id', $publication->id)
                            ->update(['title' => $publication->{'title_' . $defaultLocale}]);
                    }
                }
            });
        }

        // Copie pour 'abstract'
        if (Schema::hasColumn('publications', 'abstract_' . $defaultLocale) && Schema::hasColumn('publications', 'abstract')) {
            DB::table('publications')->orderBy('id')->chunkById(100, function ($publications) use ($defaultLocale) {
                foreach ($publications as $publication) {
                     if (isset($publication->{'abstract_' . $defaultLocale})) {
                        DB::table('publications')
                            ->where('id', $publication->id)
                            ->update(['abstract' => $publication->{'abstract_' . $defaultLocale}]);
                     }
                }
            });
        }

        Schema::table('publications', function (Blueprint $table) {
            // 3. Supprimer les nouvelles colonnes localisées
            $fieldsToDropLocalized = ['title', 'abstract'];
            foreach ($fieldsToDropLocalized as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnName = $fieldName . '_' . $locale;
                    if (Schema::hasColumn('publications', $columnName)) {
                        $table->dropColumn($columnName);
                    }
                }
            }
        });
    }
};