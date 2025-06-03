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
        Schema::table('researchers', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            
            // Déterminer après quelle colonne ajouter les nouveaux champs.
            // Idéalement après les champs 'research_interests_xx' ou 'orcid_id'.
            $afterColumn = 'orcid_id'; // Par défaut
            if (Schema::hasColumn('researchers', 'research_interests_' . ($availableLocales[0] ?? 'fr'))) {
                $afterColumn = 'research_interests_' . ($availableLocales[count($availableLocales) -1] ?? 'en');
            } elseif (!Schema::hasColumn('researchers', $afterColumn)) {
                // Fallback si orcid_id ou research_interests_xx ne sont pas trouvés, chercher un autre champ existant.
                if (Schema::hasColumn('researchers', 'google_scholar_url')) $afterColumn = 'google_scholar_url';
                // Vous pouvez ajouter d'autres fallbacks si nécessaire
            }


            foreach ($availableLocales as $locale) {
                $columnName = 'photo_alt_text_' . $locale;
                if (!Schema::hasColumn('researchers', $columnName)) {
                    if (Schema::hasColumn('researchers', $afterColumn)) {
                        $table->string($columnName)->nullable()->after($afterColumn);
                    } else {
                        $table->string($columnName)->nullable(); // Ajoute à la fin si afterColumn n'est pas trouvé
                    }
                    // Mettre à jour $afterColumn pour la prochaine colonne localisée pour ce champ
                    $afterColumn = $columnName; 
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('researchers', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $columnsToDrop = [];
            foreach ($availableLocales as $locale) {
                $columnsToDrop[] = 'photo_alt_text_' . $locale;
            }
            
            // Vérifier l'existence avant de supprimer pour éviter les erreurs
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('researchers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};