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
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Le nom de la catégorie (doit être unique)
            $table->string('slug')->unique(); // Le slug pour les URLs (doit être unique)
            $table->boolean('is_active')->default(true); // Indique si la catégorie est active
            $table->timestamps(); // created_at et updated_at
        });

        // Ajout de la colonne news_category_id à la table news
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('news_category_id')->nullable()->constrained('news_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['news_category_id']);
            $table->dropColumn('news_category_id');
        });

        Schema::dropIfExists('news_categories');
    }
};