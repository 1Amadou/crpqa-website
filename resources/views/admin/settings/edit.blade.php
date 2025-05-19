@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paramètres Généraux du Site') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md">
                    <strong class="font-bold">{{ __('Oups ! Il y a eu quelques problèmes avec votre saisie.') }}</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                {{-- Laravel ne supporte pas PUT/PATCH directement pour les formulaires HTML, mais la route POST est ok pour ce cas --}}
                {{-- Si vous préférez utiliser PUT, vous pouvez ajouter @method('PUT') et changer la route en Route::put(...) --}}

                {{-- Section: Informations Générales --}}
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Informations Générales') }}</h3>
                    <div class="space-y-6">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700">{{ __('Nom du Site') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings->site_name) }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('site_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700">{{ __('Logo Principal') }}</label>
                                @if($settings->logo_path && Storage::disk('public')->exists($settings->logo_path))
                                    <div class="mt-2 mb-1">
                                        <img src="{{ Storage::url($settings->logo_path) }}?t={{ time() }}" alt="Logo actuel" class="max-h-20 h-auto rounded border p-1 shadow-sm">
                                        <div class="mt-1">
                                            <input type="checkbox" name="remove_logo" id="remove_logo" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            <label for="remove_logo" class="ml-2 text-xs text-gray-600">{{ __('Supprimer le logo actuel') }}</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="logo" id="logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">Formats : jpg, png, gif, svg, webp. Max 2MB.</p>
                                @error('logo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="favicon" class="block text-sm font-medium text-gray-700">{{ __('Favicon') }}</label>
                                @if($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path))
                                     <div class="mt-2 mb-1">
                                        <img src="{{ Storage::url($settings->favicon_path) }}?t={{ time() }}" alt="Favicon actuel" class="max-h-10 h-auto w-10 rounded border p-1 shadow-sm">
                                         <div class="mt-1">
                                            <input type="checkbox" name="remove_favicon" id="remove_favicon" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                            <label for="remove_favicon" class="ml-2 text-xs text-gray-600">{{ __('Supprimer le favicon actuel') }}</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="favicon" id="favicon" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">Formats : ico, png, svg. Max 512KB.</p>
                                @error('favicon') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="footer_text" class="block text-sm font-medium text-gray-700">{{ __('Texte du Pied de Page') }}</label>
                            <textarea name="footer_text" id="footer_text" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('footer_text', $settings->footer_text) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Ex: © {{ date('Y') }} {{ $settings->site_name ?: config('app.name') }}. Tous droits réservés.</p>
                            @error('footer_text') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Coordonnées & Contact --}}
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Coordonnées & Contact') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">{{ __('Email de Contact Principal') }}</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('contact_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">{{ __('Téléphone de Contact Principal') }}</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('contact_phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">{{ __('Adresse Physique') }}</label>
                            <textarea name="address" id="address" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('address', $settings->address) }}</textarea>
                            @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="maps_url" class="block text-sm font-medium text-gray-700">{{ __('Lien Google Maps (URL complète)') }}</label>
                            <input type="url" name="maps_url" id="maps_url" value="{{ old('maps_url', $settings->maps_url) }}" placeholder="https://maps.google.com/..." class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('maps_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Réseaux Sociaux --}}
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Réseaux Sociaux (URL complètes)') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700">Facebook</label>
                            <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('facebook_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700">Twitter (X)</label>
                            <input type="url" name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('twitter_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                            <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $settings->linkedin_url) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('linkedin_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700">YouTube</label>
                            <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url', $settings->youtube_url) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('youtube_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Conformité & Politiques --}}
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Conformité & Politiques') }}</h3>
                    <div class="space-y-6">
                        {{-- ... cookie_consent_enabled et cookie_consent_message restent inchangés ... --}}
                        <div>
                            <label for="cookie_consent_enabled" class="font-medium text-gray-700 flex items-center">
                                <input id="cookie_consent_enabled" name="cookie_consent_enabled" type="checkbox" value="1" {{ old('cookie_consent_enabled', $settings->cookie_consent_enabled) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mr-2">
                                <span>{{ __('Activer le bandeau de consentement aux cookies') }}</span>
                            </label>
                        </div>
                        <div>
                            <label for="cookie_consent_message" class="block text-sm font-medium text-gray-700">{{ __('Message du bandeau de consentement aux cookies') }}</label>
                            <textarea name="cookie_consent_message" id="cookie_consent_message" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('cookie_consent_message', $settings->cookie_consent_message) }}</textarea>
                            @error('cookie_consent_message') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- URL de la Politique des Cookies --}}
                        <div>
                            <label for="cookie_policy_url" class="block text-sm font-medium text-gray-700">{{ __('Page de la Politique des Cookies (interne)') }}</label>
                            <select name="cookie_policy_url" id="cookie_policy_url" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('cookie_policy_url') border-red-500 @enderror">
                                <option value="">-- {{ __('Sélectionner une page ou laisser vide') }} --</option>
                                @foreach($staticPages as $slug => $title)
                                    <option value="{{ $slug }}" {{ old('cookie_policy_url', $settings->cookie_policy_url) == $slug ? 'selected' : '' }}>
                                        {{ $title }} ({{ $slug }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sélectionnez une page statique publiée pour votre politique des cookies. Le slug sera enregistré.')}}</p>
                            @error('cookie_policy_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- URL de la Politique de Confidentialité --}}
                        <div>
                            <label for="privacy_policy_url" class="block text-sm font-medium text-gray-700">{{ __('Page de la Politique de Confidentialité (interne)') }}</label>
                            <select name="privacy_policy_url" id="privacy_policy_url" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('privacy_policy_url') border-red-500 @enderror">
                                <option value="">-- {{ __('Sélectionner une page ou laisser vide') }} --</option>
                                @foreach($staticPages as $slug => $title)
                                    <option value="{{ $slug }}" {{ old('privacy_policy_url', $settings->privacy_policy_url) == $slug ? 'selected' : '' }}>
                                        {{ $title }} ({{ $slug }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sélectionnez une page statique publiée pour votre politique de confidentialité.')}}</p>
                            @error('privacy_policy_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- URL des Conditions d'Utilisation --}}
                        <div>
                            <label for="terms_of_service_url" class="block text-sm font-medium text-gray-700">{{ __('Page des Conditions d\'Utilisation (interne)') }}</label>
                            <select name="terms_of_service_url" id="terms_of_service_url" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('terms_of_service_url') border-red-500 @enderror">
                                <option value="">-- {{ __('Sélectionner une page ou laisser vide') }} --</option>
                                @foreach($staticPages as $slug => $title)
                                    <option value="{{ $slug }}" {{ old('terms_of_service_url', $settings->terms_of_service_url) == $slug ? 'selected' : '' }}>
                                        {{ $title }} ({{ $slug }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sélectionnez une page statique publiée pour vos conditions d\'utilisation.')}}</p>
                            @error('terms_of_service_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>


                {{-- Section: Paramètres E-mail & Notifications --}}
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Paramètres E-mail & Notifications') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_sender_email" class="block text-sm font-medium text-gray-700">{{ __('Email Expéditeur par Défaut (pour notifications)') }}</label>
                            <input type="email" name="default_sender_email" id="default_sender_email" value="{{ old('default_sender_email', $settings->default_sender_email) }}" placeholder="noreply@votredomaine.com" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('default_sender_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="default_sender_name" class="block text-sm font-medium text-gray-700">{{ __('Nom Expéditeur par Défaut') }}</label>
                            <input type="text" name="default_sender_name" id="default_sender_name" value="{{ old('default_sender_name', $settings->default_sender_name) }}" placeholder="{{ $settings->site_name ?: config('app.name') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('default_sender_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                {{-- Section: Intégrations & Services --}}
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Intégrations & Services') }}</h3>
                     <div>
                        <label for="google_analytics_id" class="block text-sm font-medium text-gray-700">{{ __('ID Google Analytics (Ex: UA-XXXXX-Y ou G-XXXXXXX)') }}</label>
                        <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings->google_analytics_id) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('google_analytics_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Section: Mode Maintenance --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Mode Maintenance') }}</h3>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="maintenance_mode" name="maintenance_mode" type="checkbox" value="1" {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="maintenance_mode" class="font-medium text-gray-700">{{ __('Activer le mode maintenance') }}</label>
                                <p class="text-gray-500">{{ __('Si activé, seuls les administrateurs connectés pourront accéder au site. Un message sera affiché aux visiteurs.') }}</p>
                            </div>
                        </div>
                        <div>
                            <label for="maintenance_message" class="block text-sm font-medium text-gray-700">{{ __('Message du mode maintenance') }}</label>
                            <textarea name="maintenance_message" id="maintenance_message" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('maintenance_message', $settings->maintenance_message) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Ex: Le site est actuellement en maintenance. Nous serons de retour bientôt.</p>
                            @error('maintenance_message') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>


                {{-- Actions --}}
                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Enregistrer les Paramètres') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{--
        Note pour le JavaScript (à externaliser si besoin pour des prévisualisations d'image logo/favicon dynamiques) :
        Des fonctions de prévisualisation d'image pourraient être ajoutées ici, similaires à celles
        utilisées pour les partenaires ou les événements, si vous souhaitez un retour visuel immédiat
        lors du choix d'un nouveau logo ou favicon. Elles devraient être appelées par l'attribut `onchange`
        des inputs de type 'file' et définies dans vos fichiers JS globaux.
    --}}
@endsection