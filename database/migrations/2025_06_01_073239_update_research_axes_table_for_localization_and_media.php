<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Champs à localiser et leurs types originaux
        $fieldsToLocalize = [
            'name' => 'string',             // De create_research_axes_table
            'subtitle' => 'string',         // De create_research_axes_table
            'description' => 'text',        // De create_research_axes_table
            'meta_title' => 'string',       // De create_research_axes_table
            'meta_description' => 'text',   // De create_research_axes_table
            'icon_svg' => 'text',           // Nouveau champ texte pour SVG, traduisible
            'cover_image_alt_text' => 'string', // Nouveau champ pour l'alt de l'image de couverture
        ];

        $availableLocales = config('app.available_locales', ['fr', 'en']);
        $defaultLocale = config('app.locale', 'fr');

        Schema::table('research_axes', function (Blueprint $table) use ($fieldsToLocalize, $availableLocales) {
            // 1. Ajouter les nouvelles colonnes localisées si elles n'existent pas
            foreach ($fieldsToLocalize as $fieldName => $originalType) {
                foreach ($availableLocales as $locale) {
                    $newColumnName = $fieldName . '_' . $locale;
                    if (!Schema::hasColumn('research_axes', $newColumnName)) {
                        if ($originalType === 'longText' || $originalType === 'text') {
                            $table->text($newColumnName)->nullable();
                        } else { // string
                            $table->string($newColumnName)->nullable();
                        }
                        // Optionnel: clause ->after()
                    }
                }
            }

            // 2. Ajouter le champ color_hex s'il n'existe pas
            if (!Schema::hasColumn('research_axes', 'color_hex')) {
                // Placer après icon_class ou un autre champ pertinent
                $afterColor = Schema::hasColumn('research_axes', 'icon_class') ? 'icon_class' : 'description'; 
                $table->string('color_hex', 7)->nullable()->after($afterColor); // #RRGGBB
            }
        });

        // 3. Copier les données des anciennes colonnes vers les nouvelles pour la langue par défaut
        if (in_array($defaultLocale, $availableLocales)) {
            // Ne copier que les champs qui existaient avant en version non localisée
            $fieldsToCopyData = ['name', 'subtitle', 'description', 'meta_title', 'meta_description'];
            foreach ($fieldsToCopyData as $fieldName) {
                $newColumnNameDefaultLocale = $fieldName . '_' . $defaultLocale;
                if (Schema::hasColumn('research_axes', $fieldName) && Schema::hasColumn('research_axes', $newColumnNameDefaultLocale)) {
                    DB::table('research_axes')->orderBy('id')->chunkById(100, function ($axes) use ($fieldName, $newColumnNameDefaultLocale) {
                        foreach ($axes as $axis) {
                            if (isset($axis->{$fieldName})) {
                                DB::table('research_axes')
                                    ->where('id', $axis->id)
                                    ->where(function ($query) use ($newColumnNameDefaultLocale) {
                                        $query->whereNull($newColumnNameDefaultLocale)->orWhere($newColumnNameDefaultLocale, '');
                                    })
                                    ->update([$newColumnNameDefaultLocale => $axis->{$fieldName}]);
                            }
                        }
                    });
                }
            }
        }

        Schema::table('research_axes', function (Blueprint $table) {
            // 4. Supprimer les anciennes colonnes non localisées
            $oldColumnsToDrop = ['name', 'subtitle', 'description', 'meta_title', 'meta_description'];
            foreach ($oldColumnsToDrop as $column) {
                if (Schema::hasColumn('research_axes', $column)) {
                    $table->dropColumn($column);
                }
            }

            // 5. Supprimer l'ancienne colonne cover_image_path
            if (Schema::hasColumn('research_axes', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_axes', function (Blueprint $table) {
            $availableLocales = config('app.available_locales', ['fr', 'en']);
            $defaultLocale = config('app.locale', 'fr');

            $fieldsToRestore = [
                'name' => 'string',
                'subtitle' => 'string',
                'description' => 'text',
                'meta_title' => 'string',
                'meta_description' => 'text',
                'cover_image_path' => 'string',
            ];
            
            // 1. Recréer les anciennes colonnes non localisées
            if (!Schema::hasColumn('research_axes', 'name') && Schema::hasColumn('research_axes', 'slug')) {
                $table->string('name')->nullable()->after('slug'); // Était NOT NULL
            }
            if (!Schema::hasColumn('research_axes', 'subtitle') && Schema::hasColumn('research_axes', 'name')) {
                $table->string('subtitle')->nullable()->after('name');
            }
            if (!Schema::hasColumn('research_axes', 'description') && Schema::hasColumn('research_axes', 'subtitle')) {
                $table->text('description')->nullable()->after('subtitle'); // Était NOT NULL
            }
            if (!Schema::hasColumn('research_axes', 'cover_image_path') && Schema::hasColumn('research_axes', 'icon_class')) {
                $table->string('cover_image_path')->nullable()->after('icon_class');
            }
            if (!Schema::hasColumn('research_axes', 'meta_title') && Schema::hasColumn('research_axes', 'display_order')) { // Approx.
                $table->string('meta_title')->nullable()->after('display_order');
            }
            if (!Schema::hasColumn('research_axes', 'meta_description') && Schema::hasColumn('research_axes', 'meta_title')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }


            // 2. Copier les données de la langue par défaut vers les anciennes colonnes
            if (in_array($defaultLocale, $availableLocales)) {
                $fieldsToCopyBack = ['name', 'subtitle', 'description', 'meta_title', 'meta_description'];
                foreach ($fieldsToCopyBack as $fieldName) {
                    $localizedColumnName = $fieldName . '_' . $defaultLocale;
                    if (Schema::hasColumn('research_axes', $localizedColumnName) && Schema::hasColumn('research_axes', $fieldName)) {
                        DB::table('research_axes')->orderBy('id')->chunkById(100, function ($axes) use ($fieldName, $localizedColumnName) {
                            foreach ($axes as $axis) {
                                 if (isset($axis->{$localizedColumnName})) {
                                     DB::table('research_axes')
                                        ->where('id', $axis->id)
                                        ->update([$fieldName => $axis->{$localizedColumnName}]);
                                 }
                            }
                        });
                    }
                }
            }

            // 3. Supprimer les nouvelles colonnes localisées
            $fieldsToDropLocalized = ['name', 'subtitle', 'description', 'meta_title', 'meta_description', 'icon_svg', 'cover_image_alt_text'];
            foreach ($fieldsToDropLocalized as $fieldName) {
                foreach ($availableLocales as $locale) {
                    $columnName = $fieldName . '_' . $locale;
                    if (Schema::hasColumn('research_axes', $columnName)) {
                        $table->dropColumn($columnName);
                    }
                }
            }

            // 4. Supprimer color_hex
            if (Schema::hasColumn('research_axes', 'color_hex')) {
                $table->dropColumn('color_hex');
            }
        });
    }
};