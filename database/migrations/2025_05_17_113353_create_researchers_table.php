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
            $table->id();
            $table->string('slug')->unique();
            $table->string('first_name_fr')->nullable();
            $table->string('first_name_en')->nullable();
            $table->string('last_name_fr')->nullable();
            $table->string('last_name_en')->nullable();
            $table->string('title_position_fr')->nullable();
            $table->string('title_position_en')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('biography_fr')->nullable();
            $table->text('biography_en')->nullable();
            $table->text('research_interests_fr')->nullable();
            $table->text('research_interests_en')->nullable();
            $table->string('website_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('researchgate_url')->nullable();
            $table->string('google_scholar_url')->nullable();
            $table->string('orcid_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->timestamps();
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