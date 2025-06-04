{{-- Props attendues du parent (edit.blade.php) : $settings, $availableLocales, $primaryLocale --}}
{{-- $settings est l'instance unique du modèle SiteSetting --}}

<div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg mt-8"> {{-- mt-8 pour espacer du partiel précédent --}}
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
        {{ __('Gestion des Médias Globaux du Site') }}
    </h3>

    <div class="space-y-8">
        @php
            // Définition des champs médias uniques et de leurs configurations
            $singleMediaConfigurations = [
                'favicon' => [
                    'label' => __('Favicon (.ico, .png, .svg)'),
                    'collection_name' => 'favicon', // Doit correspondre à la collection dans SiteSetting.php
                    'accept' => '.ico,.png,.svg',
                    'current_media_url_accessor' => 'favicon_url', // Accesseur dans SiteSetting.php
                    'thumb_conversion' => null, // Pas de miniature spécifique pour favicon généralement
                    'notes' => __('Taille recommandée: 32x32, 48x48 ou SVG.'),
                ],
                'logo_header' => [
                    'label' => __('Logo Principal (Header)'),
                    'collection_name' => 'logo_header',
                    'accept' => 'image/png,image/jpeg,image/svg+xml,image/webp',
                    'current_media_url_accessor' => 'logo_header_url',
                    'thumb_conversion' => null, // Afficher l'original ou une petite conversion si définie
                    'notes' => __('Format SVG recommandé pour une meilleure qualité.'),
                ],
                'logo_footer_dark' => [
                    'label' => __('Logo Pied de Page (pour Fond Sombre)'),
                    'collection_name' => 'logo_footer_dark',
                    'accept' => 'image/png,image/jpeg,image/svg+xml,image/webp',
                    'current_media_url_accessor' => 'logo_footer_dark_url',
                    'thumb_conversion' => null,
                    'notes' => __('Logo optimisé pour les pieds de page à fond sombre.'),
                ],
                 'logo_footer_light' => [
                    'label' => __('Logo Pied de Page (pour Fond Clair)'),
                    'collection_name' => 'logo_footer_light',
                    'accept' => 'image/png,image/jpeg,image/svg+xml,image/webp',
                    'current_media_url_accessor' => 'logo_footer_light_url',
                    'thumb_conversion' => null,
                    'notes' => __('Logo optimisé pour les pieds de page à fond clair.'),
                ],
                'hero_background_image' => [
                    'label' => __('Image de Fond Principale (Section Héros)'),
                    'collection_name' => 'hero_background_image',
                    'accept' => 'image/jpeg,image/png,image/webp',
                    'current_media_url_accessor' => 'hero_background_image_url',
                    'thumb_conversion' => 'thumbnail', // Supposant une conversion 'thumbnail'
                    'notes' => __('Image de fond statique pour la section héros si le slider n\'est pas utilisé ou comme fallback.'),
                ],
                'about_home_image' => [
                    'label' => __('Image Section "À Propos" (Accueil)'),
                    'collection_name' => 'about_home_image',
                    'accept' => 'image/jpeg,image/png,image/webp',
                    'current_media_url_accessor' => 'about_home_image_url',
                    'thumb_conversion' => 'thumbnail',
                ],
                'home_cta_bg_image' => [
                    'label' => __('Image de Fond Section CTA (Accueil)'),
                    'collection_name' => 'home_cta_bg_image',
                    'accept' => 'image/jpeg,image/png,image/webp',
                    'current_media_url_accessor' => 'home_cta_bg_image_url',
                    'thumb_conversion' => 'banner', // Supposant une conversion 'banner'
                ],
                'default_og_image' => [
                    'label' => __('Image Open Graph par Défaut pour le Site'),
                    'collection_name' => 'default_og_image',
                    'accept' => 'image/jpeg,image/png,image/webp',
                    'current_media_url_accessor' => 'default_og_image_url',
                    'thumb_conversion' => 'thumbnail',
                    'notes' => __('Format recommandé: 1200x630 pixels.'),
                ],
            ];
        @endphp

        @foreach($singleMediaConfigurations as $fieldName => $config)
        <div class="border-b border-gray-100 dark:border-gray-700 pb-6 mb-6">
            <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $config['label'] }}</label>
            @php $currentMediaItem = $settings->getFirstMedia($config['collection_name']); @endphp
            @if($currentMediaItem)
                <div class="mt-2 mb-2">
                    @if($config['collection_name'] === 'favicon')
                        <img src="{{ $settings->{$config['current_media_url_accessor']} }}" alt="{{ __('Favicon actuel') }}" class="h-8 w-8 object-contain border border-gray-200 dark:border-gray-700 p-1 bg-gray-50 dark:bg-gray-700/50">
                    @else
                        <img src="{{ $currentMediaItem->getUrl($config['thumb_conversion'] ?: '') }}" alt="{{ __('Aperçu actuel') }}" class="max-h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700 p-1 bg-gray-50 dark:bg-gray-700/50">
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $currentMediaItem->name }} ({{ $currentMediaItem->human_readable_size }})
                    </p>
                    <label for="remove_{{ $fieldName }}" class="inline-flex items-center mt-1 text-xs">
                        <input type="checkbox" name="remove_{{ $fieldName }}" id="remove_{{ $fieldName }}" value="1" class="form-checkbox-simple">
                        <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer') }}</span>
                    </label>
                </div>
            @endif
            <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" accept="{{ $config['accept'] }}" class="form-input-file mt-1">
            @if(isset($config['notes']))
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $config['notes'] }}</p>
            @endif
            @error($fieldName) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        @endforeach

        {{-- Collection hero_banner_images (multiple) --}}
        <div class="border-b border-gray-100 dark:border-gray-700 pb-6 mb-6">
            <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Images de Bannière Héros (Slider)') }}</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                {{__('Téléchargez plusieurs images pour un effet de carrousel. Les textes alternatifs pour chaque nouvelle image sont gérés ci-dessous.')}} <br>
                {{__('Le texte alternatif global (défini dans l\'onglet de langue "Section Héros") sera utilisé si aucun texte spécifique n\'est fourni pour une image existante.')}}
            </p>
            
            @php $heroBannerMediaItems = $settings->getMedia('hero_banner_images'); @endphp
            @if($heroBannerMediaItems->isNotEmpty())
                <div class="my-3 space-y-3">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{__('Images actuelles pour la bannière :')}}</p>
                    @foreach($heroBannerMediaItems as $media)
                    <div class="flex items-center justify-between p-2 border dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-700/30">
                        <div class="flex items-center">
                            <img src="{{ $media->getUrl() }}" alt="Image {{ $loop->iteration }} - {{ $media->name }}" class="h-12 w-auto object-contain rounded mr-3 border dark:border-gray-600">
                            <div class="text-xs">
                                <p class="text-gray-700 dark:text-gray-200">{{ $media->name }}</p>
                                <p class="text-gray-500 dark:text-gray-400">({{ $media->human_readable_size }})</p>
                                @foreach($availableLocales as $locale)
                                    @if($media->getCustomProperty('alt_text_' . $locale))
                                    <p class="text-gray-400 dark:text-gray-500 text-xxs italic">Alt ({{strtoupper($locale)}}): {{ Str::limit($media->getCustomProperty('alt_text_' . $locale), 30) }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <label class="text-xs whitespace-nowrap ml-2">
                            <input type="checkbox" name="remove_specific_hero_banner_images[]" value="{{ $media->id }}" class="form-checkbox-simple">
                            {{__('Supprimer')}}
                        </label>
                    </div>
                    @endforeach
                    @if($heroBannerMediaItems->count() > 0)
                    <label for="remove_hero_banner_images_all" class="inline-flex items-center mt-1 text-xs">
                        <input type="checkbox" name="remove_hero_banner_images_all" id="remove_hero_banner_images_all" value="1" class="form-checkbox-simple">
                        <span class="ml-2 text-gray-600 dark:text-gray-400">{{ __('Supprimer TOUTES les images de la bannière') }}</span>
                    </label>
                    @endif
                </div>
            @endif
            
            <div>
                <label for="hero_banner_images" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mt-3">{{__('Ajouter de nouvelles images pour la bannière :')}}</label>
                <input type="file" name="hero_banner_images[]" id="hero_banner_images" multiple accept="image/jpeg,image/png,image/webp"
                       class="form-input-file mt-1">
                @error('hero_banner_images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('hero_banner_images') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                
                {{-- Champs pour les textes alternatifs des NOUVELLES images de la bannière --}}
                <div id="hero_banner_alt_text_fields_container" class="mt-3 space-y-2">
                    {{-- Le JavaScript remplira cette section --}}
                </div>
                 @error('hero_banner_alt_text.*.*') <p class="text-red-500 text-xs mt-1">{{ __('Veuillez vérifier les textes alternatifs des nouvelles images.') }}</p> @enderror
            </div>
        </div>
    </div>
</div>
{{-- Le script pour les onglets de langue de cette section est dans edit.blade.php principal --}}
{{-- Le script pour hero_banner_alt_text_fields_container est dans edit.blade.php principal --}}