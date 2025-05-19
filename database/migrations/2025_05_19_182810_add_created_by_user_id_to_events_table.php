<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')
                  ->nullable() // Important pour la flexibilité
                  ->after('is_featured') // Ou après un autre champ pertinent
                  ->constrained('users')
                  ->onDelete('set null'); // ou 'cascade'
        });
    }
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn('created_by_user_id');
        });
    }
};