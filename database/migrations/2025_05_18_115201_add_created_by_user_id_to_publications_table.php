<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la colonne created_by_user_id à la table publications.
     */
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')
                  ->nullable()
                  ->after('is_featured') // Position de la colonne (modifiable si besoin)
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Garde la publication même si l'utilisateur est supprimé
        });
    }

    /**
     * Supprime la colonne created_by_user_id de la table publications.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn('created_by_user_id');
        });
    }
};
