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
        Schema::table('events', function (Blueprint $table) {
            // Ajoute le champ meta_title après la colonne 'slug'
            $table->string('meta_title')->nullable()->after('slug');
            // Ajoute le champ meta_description après la nouvelle colonne 'meta_title'
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Supprime les colonnes si la migration est annulée
            $table->dropColumn(['meta_title', 'meta_description']);
        });
    }
};