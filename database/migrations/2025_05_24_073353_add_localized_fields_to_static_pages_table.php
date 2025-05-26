<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            // Supposer que 'fr' et 'en' sont vos locales
            // Pour title
            $table->string('title_fr')->nullable()->after('title');
            $table->string('title_en')->nullable()->after('title_fr');
            // Pour content
            $table->longText('content_fr')->nullable()->after('content');
            $table->longText('content_en')->nullable()->after('content_fr');
            // Pour meta_title
            $table->string('meta_title_fr')->nullable()->after('meta_title');
            $table->string('meta_title_en')->nullable()->after('meta_title_fr');
            // Pour meta_description
            $table->text('meta_description_fr')->nullable()->after('meta_description');
            $table->text('meta_description_en')->nullable()->after('meta_description_fr');

            // Vous pouvez choisir de rendre les colonnes de base (title, content, etc.) nullables
            // ou de les supprimer si les versions localisées sont les seules sources de vérité.
            // Pour l'instant, on les garde et on ajoute les versions localisées.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $columnsToDrop = [
                'title_fr', 
                'title_en', 
                'content_fr', 
                'content_en', 
                'meta_title_fr', 
                'meta_title_en', 
                'meta_description_fr', 
                'meta_description_en'
            ];

            foreach ($columnsToDrop as $column) {
                // Vérifie si la colonne existe dans la table 'static_pages' avant de la supprimer
                if (Schema::hasColumn('static_pages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};