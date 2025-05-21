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
        Schema::table('news', function (Blueprint $table) {
            // Ajoute la colonne 'is_published'
            // Elle sera booléenne et par défaut à true pour les articles existants
            // Place-la après 'published_at' pour une bonne organisation si tu veux
            $table->boolean('is_published')->default(true)->after('published_at');

            // Ajoute la colonne 'image_alt'
            // Elle sera de type string et pourra être nulle
            // Place-la après 'cover_image_path'
            $table->string('image_alt')->nullable()->after('cover_image_path');

            // Renomme 'summary' en 'short_content' si tu préfères le nom initialement mentionné
            // Si 'summary' existe déjà et que tu veux changer son nom en 'short_content'
            // Assure-toi que tu n'as pas de données importantes dedans si tu n'es pas sûr.
            // Si 'summary' est ton nom final, tu peux ignorer cette ligne.
            // $table->renameColumn('summary', 'short_content'); 

            // Renomme 'cover_image_path' en 'image_url' si tu préfères le nom initialement mentionné
            // Si 'cover_image_path' est ton nom final, tu peux ignorer cette ligne.
            // $table->renameColumn('cover_image_path', 'image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // N'oublie pas de drop les colonnes dans l'ordre inverse de leur ajout
            $table->dropColumn('is_published');
            $table->dropColumn('image_alt');

            // Inverse les renommages de colonnes si tu les as appliqués dans 'up()'
            // $table->renameColumn('short_content', 'summary');
            // $table->renameColumn('image_url', 'cover_image_path');
        });
    }
};