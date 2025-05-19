<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_target_audience_to_events_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('target_audience')->nullable()->after('description'); // Ou après un autre champ pertinent
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('target_audience');
        });
    }
};