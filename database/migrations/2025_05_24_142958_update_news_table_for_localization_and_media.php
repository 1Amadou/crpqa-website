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
            // Noms des champs de base à localiser (issus de $localizedFields dans News.php)
            $baseTextFields = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt'];
            // Types correspondants (approximatifs, ajustez si nécessaire)
            // title, meta_title, cover_image_alt => string(255)
            // summary, meta_description => text
            // content => longText
            $fieldTypes = [
                'title' => 'string',
                'summary' => 'text', // Ou 'string' si c'était une chaîne courte
                'content' => 'longText',
                'meta_title' => 'string',
                'meta_description' => 'text', // Ou 'string' si c'était une chaîne courte
                'cover_image_alt' => 'string',
            ];

            $availableLocales = config('app.available_locales', ['fr', 'en']);

            foreach ($baseTextFields as $field) {
                $originalColumnType = $fieldTypes[$field] ?? 'string'; // Type par défaut si non spécifié

                foreach ($availableLocales as $locale) {
                    $columnName = $field . '_' . $locale;
                    // Ajoute la colonne localisée après la colonne de base si elle existe, sinon à la fin
                    if (Schema::hasColumn('news', $field)) {
                        $table->{$originalColumnType}($columnName)->nullable()->after($field);
                    } elseif (Schema::hasColumn('news', 'slug')) { // Autre colonne de référence pour 'after'
                         $table->{$originalColumnType}($columnName)->nullable()->after('slug');
                    } else {
                        $table->{$originalColumnType}($columnName)->nullable();
                    }
                }
            }

            // Optionnel mais recommandé : Copier les données existantes des anciennes colonnes
            // vers la colonne de la langue par défaut (ex: 'fr')
            // Vous devez exécuter cela AVANT de supprimer les anciennes colonnes.
            // Cette partie est commentée car elle nécessite une exécution soignée et une adaptation.
            // Exemple si 'fr' est votre langue par défaut et que les anciennes colonnes existent :
            
            if (Schema::hasColumn('news', 'title') && Schema::hasColumn('news', 'title_fr')) {
                DB::statement("UPDATE news SET title_fr = title WHERE title_fr IS NULL OR title_fr = ''");
            }
            if (Schema::hasColumn('news', 'summary') && Schema::hasColumn('news', 'summary_fr')) {
                DB::statement("UPDATE news SET summary_fr = summary WHERE summary_fr IS NULL OR summary_fr = ''");
            }
            if (Schema::hasColumn('news', 'content') && Schema::hasColumn('news', 'content_fr')) {
                DB::statement("UPDATE news SET content_fr = content WHERE content_fr IS NULL OR content_fr = ''");
            }
            // Répétez pour meta_title, meta_description, cover_image_alt
            // ...

            // Une fois les données copiées, vous POUVEZ supprimer les anciennes colonnes de base non localisées
            // ATTENTION : Ceci est destructif pour les anciennes colonnes. Assurez-vous d'avoir copié les données.
            $table->dropColumn(['title', 'summary', 'content', 'meta_title', 'meta_description', 'cover_image_alt']);
           

            // Supprimer l'ancienne colonne pour le chemin de l'image si elle existe
            // (votre modèle News.php avait 'cover_image_url' dans fillable,
            // mais la migration originale 'create_news_table' avait 'cover_image_path')
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
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $columnsToDrop = [];
            foreach ($baseTextFields as $field) {
                foreach ($availableLocales as $locale) {
                    $columnsToDrop[] = $field . '_' . $locale;
                }
            }
            $table->dropColumn($columnsToDrop);

            // Si vous aviez supprimé les colonnes de base dans up(), recréez-les ici.
            // Exemple :
            // $table->string('title')->nullable(); // Ou le type original
            // $table->text('summary')->nullable();
            // ...

            // Recréer l'ancienne colonne d'image si besoin (ajustez le nom et le type si nécessaire)
            // $table->string('cover_image_path')->nullable();
        });
    }
};