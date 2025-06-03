@props([
    'category' => null, // Sera un objet NewsCategory (ou null pour la création)
])

{{-- CSRF est dans le formulaire principal (create/edit) --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
    {{-- Nom de la Catégorie --}}
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nom de la Catégorie') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $category?->name) }}"
               required
               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Slug --}}
    <div class="mb-4">
        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug (URL)') }}</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $category?->slug) }}"
               class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
               placeholder="{{ __('Sera généré automatiquement si laissé vide') }}">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Basé sur le nom. Uniquement minuscules, chiffres, tirets.')}}</p>
        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
    {{-- Couleur de Fond --}}
    <div class="mb-4">
        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Couleur de Fond (Optionnel)') }}</label>
        <input type="color" name="color" id="color" value="{{ old('color', $category?->color ?? '#EFF6FF') }}"
               class="mt-1 block w-full h-10 px-1 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Ex: #EFF6FF pour un bleu clair. Utilisé pour les badges.')}}</p>
        @error('color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Couleur du Texte --}}
    <div class="mb-4">
        <label for="text_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Couleur du Texte (Optionnel)') }}</label>
        <input type="color" name="text_color" id="text_color" value="{{ old('text_color', $category?->text_color ?? '#1D4ED8') }}"
               class="mt-1 block w-full h-10 px-1 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Ex: #1D4ED8 pour un bleu foncé. Assurez un bon contraste.')}}</p>
        @error('text_color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Statut Actif --}}
<div class="mt-6">
    <label for="is_active" class="flex items-center cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}
               class="sr-only peer">
        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Catégorie Active') }}</span>
    </label>
    @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>