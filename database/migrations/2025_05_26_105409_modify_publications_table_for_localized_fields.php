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
        Schema::table('publications', function (Blueprint $table) {
            // 1. Ajouter les nouvelles colonnes pour les traductions
            // Assurez-vous que 'fr' et 'en' correspondent à vos locales.
            // Ajustez ->after() pour placer les colonnes où vous le souhaitez.
            // Si la colonne 'type' existe, elles peuvent venir après.
            // Sinon, ajustez en fonction de votre structure.
            $table->text('title_fr')->nullable()->after('slug'); // Exemple de positionnement
            $table->text('title_en')->nullable()->after('title_fr');
            $table->longText('abstract_fr')->nullable()->after('title_en');
            $table->longText('abstract_en')->nullable()->after('abstract_fr');

            // 2. Supprimer les anciennes colonnes JSON si elles existent
            // (en supposant qu'elles s'appelaient 'title' et 'abstract')
            // Vérifiez les noms exacts de vos colonnes JSON originales.
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
            // 1. Supprimer les nouvelles colonnes traduites
            // Assurez-vous que 'fr' et 'en' correspondent à vos locales.
            if (Schema::hasColumn('publications', 'title_fr')) {
                $table->dropColumn('title_fr');
            }
            if (Schema::hasColumn('publications', 'title_en')) {
                $table->dropColumn('title_en');
            }
            if (Schema::hasColumn('publications', 'abstract_fr')) {
                $table->dropColumn('abstract_fr');
            }
            if (Schema::hasColumn('publications', 'abstract_en')) {
                $table->dropColumn('abstract_en');
            }

            // 2. Recréer les anciennes colonnes JSON (si vous souhaitez un rollback "complet")
            // (en supposant qu'elles s'appelaient 'title' et 'abstract')
            // Ajustez ->after() en fonction de votre structure si vous les recréez.
            $table->json('title')->nullable()->after('slug'); // Exemple de positionnement
            $table->json('abstract')->nullable()->after('title');
        });
    }
};