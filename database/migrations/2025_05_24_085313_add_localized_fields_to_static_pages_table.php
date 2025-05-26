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
        // Schema::table('static_pages', function (Blueprint $table) {
        //     // Supposons que la migration '2025_05_24_073353_add_localized_fields_to_static_pages_table'
        //     // s'occupe déjà de créer 'title_fr', 'title_en', 'content_fr', 'content_en', etc.
        //     // Si c'est le cas, cette section doit être vide ou ne pas redéclarer ces colonnes.

        //     // Exemple de ce qui pourrait causer l'erreur (NE PAS FAIRE SI DÉJÀ FAIT AILLEURS):
        //     // $table->string('title_fr')->nullable()->after('title');
        //     // $table->string('title_en')->nullable()->after('title_fr');
        //     // ... et ainsi de suite pour les autres champs localisés.
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('static_pages', function (Blueprint $table) {
        //     // Si la méthode up() de CETTE migration ne crée plus ces colonnes,
        //     // alors la méthode down() ne doit pas non plus essayer de les supprimer.
        //     // La responsabilité de la suppression incombe à la migration qui les a créées.

        //     // Exemple de ce qui ne devrait plus être ici si 'up()' est vide pour ces colonnes :
        //     // $columnsToDrop = [
        //     //     'title_fr', 'title_en', 
        //     //     'content_fr', 'content_en', 
        //     //     // ... autres colonnes
        //     // ];
        //     // foreach ($columnsToDrop as $column) {
        //     //     if (Schema::hasColumn('static_pages', $column)) {
        //     //         $table->dropColumn($column);
        //     //     }
        //     // }
        // });
    }
};