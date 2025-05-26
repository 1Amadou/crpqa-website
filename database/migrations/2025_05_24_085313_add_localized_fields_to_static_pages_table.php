<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            // Supposons 'fr' et 'en' comme locales
            // Pour title (qui est string dans la migration originale)
            $table->string('title_fr')->nullable()->after('title');
            $table->string('title_en')->nullable()->after('title_fr');

            // Pour content (qui est longText)
            $table->longText('content_fr')->nullable()->after('content');
            $table->longText('content_en')->nullable()->after('content_fr');

            // Pour meta_title (qui est string)
            $table->string('meta_title_fr')->nullable()->after('meta_title');
            $table->string('meta_title_en')->nullable()->after('meta_title_fr');

            // Pour meta_description (qui est text)
            $table->text('meta_description_fr')->nullable()->after('meta_description');
            $table->text('meta_description_en')->nullable()->after('meta_description_fr');

            // Optionnel: Rendre les colonnes de base (title, content) nullables ou les supprimer
            // $table->string('title')->nullable()->change();
            // $table->longText('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn([
                'title_fr', 'title_en',
                'content_fr', 'content_en',
                'meta_title_fr', 'meta_title_en',
                'meta_description_fr', 'meta_description_en',
            ]);
        });
    }
};