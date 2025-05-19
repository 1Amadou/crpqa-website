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
        Schema::create('researchers', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée (BIGINT UNSIGNED)
            $table->string('first_name'); // Prénom
            $table->string('last_name'); // Nom de famille
            $table->string('title')->nullable(); // Titre (Dr., Prof.), peut être vide
            $table->string('position')->nullable(); // Poste/Fonction, peut être vide
            $table->string('email')->unique()->nullable(); // Email, doit être unique, peut être vide
            $table->string('phone_number')->nullable(); // Numéro de téléphone, peut être vide
            $table->text('biography')->nullable(); // Biographie détaillée, peut être vide
            $table->string('photo_path')->nullable(); // Chemin vers la photo, peut être vide
            $table->text('research_areas')->nullable(); // Domaines de recherche, peut être vide
            $table->string('linkedin_url')->nullable(); // Lien LinkedIn, peut être vide
            $table->string('google_scholar_url')->nullable(); // Lien Google Scholar, peut être vide
            $table->boolean('is_active')->default(true); // Profil actif/visible, vrai par défaut
            $table->integer('display_order')->default(0)->nullable(); // Ordre d'affichage, 0 par défaut
            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researchers');
    }
};
