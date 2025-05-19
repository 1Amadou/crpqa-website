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
        Schema::create('publication_researcher', function (Blueprint $table) {
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');
            $table->foreignId('researcher_id')->constrained()->onDelete('cascade');
            $table->primary(['publication_id', 'researcher_id']);
            // $table->timestamps(); // Décommentez si besoin de tracer les associations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_researcher');
    }
};
