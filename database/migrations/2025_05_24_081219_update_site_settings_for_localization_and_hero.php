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
        Schema::table('site_settings', function (Blueprint $table) {
            // Champs à traduire (ajout des versions FR et EN)
            // Assurez-vous que les types correspondent à ce que vous aviez ou ce dont vous avez besoin.

            // Site Name (était string)
            $table->string('site_name_fr')->nullable()->after('site_name');
            $table->string('site_name_en')->nullable()->after('site_name_fr');

            // Address (était text)
            $table->text('address_fr')->nullable()->after('address');
            $table->text('address_en')->nullable()->after('address_fr');

            // Footer Text (était text)
            $table->text('footer_text_fr')->nullable()->after('footer_text');
            $table->text('footer_text_en')->nullable()->after('footer_text_fr');

            // Cookie Consent Message (était text)
            $table->text('cookie_consent_message_fr')->nullable()->after('cookie_consent_message');
            $table->text('cookie_consent_message_en')->nullable()->after('cookie_consent_message_fr');
            
            // Maintenance Message (était text)
            $table->text('maintenance_message_fr')->nullable()->after('maintenance_message');
            $table->text('maintenance_message_en')->nullable()->after('maintenance_message_fr');

            // Nouveaux champs pour la section Héros (traduisibles)
            $table->string('hero_title_fr')->nullable()->after('youtube_url'); // Ajustez l'ordre si besoin
            $table->string('hero_title_en')->nullable()->after('hero_title_fr');
            $table->text('hero_subtitle_fr')->nullable()->after('hero_title_en');
            $table->text('hero_subtitle_en')->nullable()->after('hero_subtitle_fr');

            // Nouveaux champs pour SEO Meta Title (traduisibles)
            $table->string('seo_meta_title_fr')->nullable()->after('site_name_en');
            $table->string('seo_meta_title_en')->nullable()->after('seo_meta_title_fr');
            $table->text('seo_meta_description_fr')->nullable()->after('seo_meta_title_en');
            $table->text('seo_meta_description_en')->nullable()->after('seo_meta_description_fr');


            // Optionnel: Conserver les liens de politique sous forme de slug de page statique (comme dans le contrôleur)
            // OU les rendre traduisibles si ce sont des URL externes.
            // Les champs comme cookie_policy_url, privacy_policy_url, terms_of_service_url sont déjà des strings.
            // Si vous voulez les traduire, ajoutez _fr, _en. Pour l'instant, on les laisse tels quels.

            // Supprimer les anciennes colonnes logo_path et favicon_path
            // car Spatie Media Library va les gérer.
            // Important : Assurez-vous d'avoir un plan pour migrer les chemins existants si besoin avant de supprimer.
            // Pour plus de sécurité, vous pouvez les renommer d'abord, puis les supprimer dans une migration ultérieure.
            // $table->dropColumn(['logo_path', 'favicon_path']);
            // Pour l'instant, nous ne les supprimons PAS pour éviter la perte de données.
            // Vous devrez les gérer manuellement ou migrer vers Spatie Media Library.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_name_fr', 'site_name_en',
                'address_fr', 'address_en',
                'footer_text_fr', 'footer_text_en',
                'cookie_consent_message_fr', 'cookie_consent_message_en',
                'maintenance_message_fr', 'maintenance_message_en',
                'hero_title_fr', 'hero_title_en',
                'hero_subtitle_fr', 'hero_subtitle_en',
                'seo_meta_title_fr', 'seo_meta_title_en',
                'seo_meta_description_fr', 'seo_meta_description_en',
            ]);

            // Si vous aviez renommé logo_path et favicon_path, ici vous les recréeriez ou remettriez les noms.
            // $table->string('logo_path')->nullable();
            // $table->string('favicon_path')->nullable();
        });
    }
};