@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un nouvel utilisateur') }}
        </h2>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium shadow-sm transition ease-in-out duration-150">
            {{ __('Annuler et retourner à la liste') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 md:p-8 border-b border-gray-200">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Colonne Principale --}}
                    <div class="md:col-span-2 space-y-6">
                        {{-- Nom --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom complet') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Adresse Email') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mot de passe --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Mot de passe') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmation du Mot de passe --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirmer le mot de passe') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    {{-- Colonne Latérale (Rôles et Profil Chercheur) --}}
                    <div class="md:col-span-1 space-y-6">
                        {{-- Rôles --}}
                        <div>
                            <label for="roles" class="block text-sm font-medium text-gray-700">{{ __('Rôles') }} <span class="text-red-500">*</span></label>
                            @if(!empty($roles) && count($roles) > 0)
                                @foreach ($roles as $roleName)
                                <div class="mt-2 flex items-center">
                                    <input type="checkbox" name="roles[]" id="role_{{ $roleName }}" value="{{ $roleName }}"
                                           {{ (is_array(old('roles')) && in_array($roleName, old('roles'))) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="role_{{ $roleName }}" class="ml-2 text-sm text-gray-700">{{ $roleName }}</label>
                                </div>
                                @endforeach
                            @else
                                <p class="mt-1 text-sm text-gray-500">Aucun rôle disponible. Veuillez d'abord créer des rôles.</p>
                            @endif
                            @error('roles')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Lier à un profil Chercheur (Optionnel, si le rôle "Chercheur" est sélectionné) --}}
                        {{-- Un script JS pourrait afficher/masquer ce champ en fonction de la sélection du rôle "Chercheur" --}}
                        <div id="researcher_assignment_section" class="mt-4" style="display: none;"> {{-- Caché par défaut --}}
                            <label for="researcher_id" class="block text-sm font-medium text-gray-700">{{ __('Lier au Profil Chercheur (Optionnel)') }}</label>
                            <select name="researcher_id" id="researcher_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('researcher_id') border-red-500 @enderror">
                                <option value="">{{ __('-- Sélectionner un profil chercheur non lié --') }}</option>
                                @if(!empty($researchers) && count($researchers) > 0)
                                    @foreach ($researchers as $id => $name)
                                        <option value="{{ $id }}" {{ old('researcher_id') == $id ? 'selected' : '' }}>
                                            {{ $name }} (ID: {{ $id }})
                                        </option>
                                    @endforeach
                                @else
                                     <option value="" disabled>{{ __('Aucun profil chercheur non lié disponible.') }}</option>
                                @endif
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Apparaît si le rôle "Chercheur" est sélectionné. Lie ce compte utilisateur à un profil chercheur existant qui n\'a pas encore de compte.') }}</p>
                            @error('researcher_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-8 mt-8 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('admin.users.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Enregistrer l\'utilisateur') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{--
        Note pour l'intégration JavaScript :
        Un script serait utile ici pour afficher la section 'researcher_assignment_section'
        uniquement si la case à cocher du rôle "Chercheur" est sélectionnée.
        Ce script devrait être placé dans vos fichiers JS globaux (ex: resources/js/admin/forms.js).

        Exemple de logique JS (à adapter) :
        const rolesCheckboxes = document.querySelectorAll('input[name="roles[]"]');
        const researcherSection = document.getElementById('researcher_assignment_section');
        const researcherRoleName = 'Chercheur'; // Ou le nom exact du rôle

        function toggleResearcherSection() {
            let researcherRoleSelected = false;
            rolesCheckboxes.forEach(checkbox => {
                if (checkbox.value === researcherRoleName && checkbox.checked) {
                    researcherRoleSelected = true;
                }
            });
            researcherSection.style.display = researcherRoleSelected ? 'block' : 'none';
        }

        rolesCheckboxes.forEach(checkbox => checkbox.addEventListener('change', toggleResearcherSection));
        document.addEventListener('DOMContentLoaded', toggleResearcherSection); // Exécuter au chargement de la page
    --}}
@endsection