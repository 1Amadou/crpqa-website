<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Pour copier les données

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Noms des champs de base à localiser
            $baseTextFields = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];
            // Types correspondants
            $fieldTypes = [
                'title' => 'string',
                'summary' => 'text',
                'content' => 'longText',
                'meta_title' => 'string',
                'meta_description' => 'text',
                'cover_image_alt' => 'string',
            ];

            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $defaultLocaleForCopy = 'fr'; // Locale par défaut pour la copie des anciennes données

            // 1. Ajouter les nouvelles colonnes localisées
            foreach ($baseTextFields as $field) {
                $originalColumnType = $fieldTypes[$field] ?? 'string'; // Type par défaut si non spécifié

                foreach ($availableLocales as $locale) {
                    $columnName = $field . '_' . $locale;
                    if (!Schema::hasColumn('news', $columnName)) { // Vérifier si la colonne n'existe pas déjà
                        $columnDefinition = $table->{$originalColumnType}($columnName)->nullable();
                        // Essayer de placer après la colonne de base, sinon après 'slug' ou à la fin
                        if (Schema::hasColumn('news', $field)) {
                            $columnDefinition->after($field);
                        } elseif (Schema::hasColumn('news', 'slug')) {
                            $columnDefinition->after('slug');
                        }
                        // Si ni 'field' ni 'slug' n'existent, elle sera ajoutée à la fin par défaut
                    }
                }
            }

            // 2. Copier les données existantes des anciennes colonnes vers la colonne de la langue par défaut
            // Assurez-vous que la locale par défaut (ex: 'fr') est dans $availableLocales
            if (in_array($defaultLocaleForCopy, $availableLocales)) {
                foreach ($baseTextFields as $field) {
                    $localizedColumnName = $field . '_' . $defaultLocaleForCopy;
                    if (Schema::hasColumn('news', $field) && Schema::hasColumn('news', $localizedColumnName)) {
                        // Copie seulement si la colonne localisée est vide ou nulle pour éviter d'écraser des données déjà localisées
                        DB::statement("UPDATE news SET $localizedColumnName = $field WHERE $localizedColumnName IS NULL OR $localizedColumnName = ''");
                    }
                }
            }

            // 3. Supprimer les anciennes colonnes de base non localisées (CONDITIONNELLEMENT)
            foreach ($baseTextFields as $field) {
                if (Schema::hasColumn('news', $field)) {
                    $table->dropColumn($field);
                }
            }
            
            // 4. Supprimer les anciennes colonnes d'image spécifiques si elles existent
            if (Schema::hasColumn('news', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
            if (Schema::hasColumn('news', 'cover_image_url')) { // Au cas où cette colonne existerait aussi
                $table->dropColumn('cover_image_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $baseTextFields = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];
            $fieldTypes = [ // Assurez-vous que ces types correspondent à ceux des colonnes originales
                'title' => 'string',
                'summary' => 'text',
                'content' => 'longText',
                'meta_title' => 'string',
                'meta_description' => 'text',
                'cover_image_alt' => 'string',
            ];
            $availableLocales = config('app.available_locales', ['fr', 'en']);

            // 1. Supprimer les colonnes localisées ajoutées par up()
            $localizedColumnsToDrop = [];
            foreach ($baseTextFields as $field) {
                foreach ($availableLocales as $locale) {
                    $localizedColumnsToDrop[] = $field . '_' . $locale;
                }
            }
            // Supprimer conditionnellement chaque colonne localisée
            foreach ($localizedColumnsToDrop as $columnName) {
                if (Schema::hasColumn('news', $columnName)) {
                    $table->dropColumn($columnName);
                }
            }

            // 2. Recréer les anciennes colonnes de base (si elles ont été supprimées par up())
            // Il est important de les recréer dans un ordre logique si 'after()' était utilisé,
            // ou simplement s'assurer qu'elles sont présentes.
            // Pour la simplicité, on les ajoute; l'ordre exact de 'after' est moins critique pour 'down'.
            foreach ($baseTextFields as $field) {
                if (!Schema::hasColumn('news', $field)) {
                    $originalColumnType = $fieldTypes[$field] ?? 'string';
                    $table->{$originalColumnType}($field)->nullable();
                }
            }

            // 3. Recréer l'ancienne colonne d'image 'cover_image_path' si elle a été supprimée par up()
            // (et si c'était la colonne standard avant cette migration)
            if (!Schema::hasColumn('news', 'cover_image_path')) {
                $table->string('cover_image_path')->nullable(); // Ajustez le type si ce n'était pas string
            }
            // Si 'cover_image_url' était aussi une colonne valide avant et supprimée, recréez-la aussi.
            // if (!Schema::hasColumn('news', 'cover_image_url')) {
            //     $table->string('cover_image_url')->nullable();
            // }

            // Note : La copie inversée des données (des champs localisés vers les champs de base)
            // lors d'un rollback est complexe (quelle locale choisir ?) et est souvent omise.
            // Le plus important est de restaurer la structure du schéma.
        });
    }
};