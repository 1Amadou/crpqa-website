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
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Le nom de la catégorie
            $table->string('slug')->unique(); // Le slug pour les URLs
            $table->string('color')->nullable(); // AJOUTÉ : Couleur de fond du badge/tag
            $table->string('text_color')->nullable(); // AJOUTÉ : Couleur du texte du badge/tag
            $table->boolean('is_active')->default(true); // Indique si la catégorie est active
            $table->timestamps(); // created_at et updated_at
        });

        // Ajout de la colonne news_category_id à la table news
        // Il est généralement préférable de faire cela dans une migration séparée pour la table 'news'
        // si la table 'news' est créée avant 'news_categories', ou si elle existe déjà.
        // Mais si l'ordre est correct (news_categories créée avant que cette colonne soit ajoutée à news),
        // cela peut rester ici. Pour plus de clarté et de séparation des préoccupations,
        // une migration dédiée 'add_news_category_id_to_news_table' serait mieux.
        // Pour l'instant, nous laissons comme vous l'aviez.
        Schema::table('news', function (Blueprint $table) {
            // S'assurer que la colonne n'existe pas déjà si on relance après un échec partiel
            if (!Schema::hasColumn('news', 'news_category_id')) {
                $table->foreignId('news_category_id')
                      ->nullable()
                      ->after('slug') // Ou une autre position pertinente dans la table 'news'
                      ->constrained('news_categories')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Vérifier si la colonne et la contrainte existent avant de les supprimer
            if (Schema::hasColumn('news', 'news_category_id')) {
                // La suppression de la contrainte se fait souvent par nom de contrainte, 
                // ou Laravel essaie de le deviner à partir des colonnes.
                // Pour plus de robustesse, on peut vérifier le nom de la contrainte si besoin.
                // Par convention: news_news_category_id_foreign
                // $table->dropForeign('news_news_category_id_foreign'); // Décommenter si dropForeign(['news_category_id']) ne suffit pas
                $table->dropForeign(['news_category_id']);
                $table->dropColumn('news_category_id');
            }
        });

        Schema::dropIfExists('news_categories');
    }
};