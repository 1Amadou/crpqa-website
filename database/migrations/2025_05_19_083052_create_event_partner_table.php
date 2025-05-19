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
        Schema::create('event_partner', function (Blueprint $table) {
            $table->id(); // Clé primaire pour la table pivot elle-même (optionnel mais bonne pratique)

            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // Si l'événement est supprimé, les liaisons sont supprimées

            $table->foreignId('partner_id')
                  ->constrained('partners')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // Si le partenaire est supprimé, les liaisons sont supprimées

            // Vous pourriez ajouter des champs supplémentaires à la table pivot si nécessaire
            // Par exemple, le type de partenariat pour CET événement spécifique
            // $table->string('partnership_type')->nullable();

            $table->timestamps(); // created_at et updated_at pour la liaison elle-même

            // Assurer que la combinaison event_id et partner_id est unique
            $table->unique(['event_id', 'partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_partner');
    }
};