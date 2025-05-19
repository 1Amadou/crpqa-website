<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('cookie_consent_enabled')->default(true)->after('footer_text');
            $table->text('cookie_consent_message')->nullable()->after('cookie_consent_enabled');
            $table->string('cookie_policy_url')->nullable()->after('cookie_consent_message');
            $table->string('privacy_policy_url')->nullable()->after('cookie_policy_url');
            $table->string('terms_of_service_url')->nullable()->after('privacy_policy_url');
            $table->string('default_sender_email')->nullable()->after('terms_of_service_url');
            $table->string('default_sender_name')->nullable()->after('default_sender_email');
            $table->string('google_analytics_id')->nullable()->after('default_sender_name');
            $table->boolean('maintenance_mode')->default(false)->after('google_analytics_id');
            $table->text('maintenance_message')->nullable()->after('maintenance_mode');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'cookie_consent_enabled',
                'cookie_consent_message',
                'cookie_policy_url',
                'privacy_policy_url',
                'terms_of_service_url',
                'default_sender_email',
                'default_sender_name',
                'google_analytics_id',
                'maintenance_mode',
                'maintenance_message',
            ]);
        });
    }
};
