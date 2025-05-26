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
            // Champs de base à rendre nullables
            // Vérifiez les types exacts de vos colonnes originales si besoin
            if (Schema::hasColumn('news', 'title')) {
                $table->string('title')->nullable()->change();
            }
            if (Schema::hasColumn('news', 'summary')) {
                $table->text('summary')->nullable()->change();
            }
            if (Schema::hasColumn('news', 'content')) {
                $table->longText('content')->nullable()->change();
            }
            if (Schema::hasColumn('news', 'meta_title')) {
                $table->string('meta_title')->nullable()->change();
            }
            if (Schema::hasColumn('news', 'meta_description')) {
                $table->text('meta_description')->nullable()->change();
            }
            // La colonne 'cover_image_alt' a été ajoutée par une migration ultérieure
            // et devrait déjà être nullable selon la migration add_is_published_and_image_alt_to_news_table.php
            // mais nous pouvons le vérifier et le rendre nullable si ce n'est pas le cas.
            if (Schema::hasColumn('news', 'cover_image_alt')) {
                $table->string('cover_image_alt')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Pour annuler, vous pourriez vouloir les remettre en NOT NULL,
            // mais cela nécessiterait qu'il n'y ait pas de valeurs NULL existantes.
            // Pour plus de simplicité, on peut les laisser nullables,
            // ou vous devez gérer la non-nullabilité avec une valeur par défaut.
            // Exemple si vous vouliez les remettre en NOT NULL (attention aux données existantes)
            // if (Schema::hasColumn('news', 'title')) {
            //     $table->string('title')->nullable(false)->change();
            // }
            // ... etc.
        });
    }
};