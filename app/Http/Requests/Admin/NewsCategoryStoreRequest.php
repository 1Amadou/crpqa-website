<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsCategoryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assurez-vous que cette permission existe et est assignée aux rôles appropriés
        return Auth::user()->can('manage news_categories'); 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:news_categories,name',
            'slug' => 'nullable|string|max:255|alpha_dash:ascii|unique:news_categories,slug',
            'color' => 'nullable|string|max:50', // Ex: #RRGGBB, ou nom de couleur Tailwind
            'text_color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);

        // Générer le slug à partir du nom si le slug est vide
        if (empty($this->slug) && !empty($this->name)) {
            $this->merge(['slug' => Str::slug($this->name)]);
        } elseif (!empty($this->slug)) {
            $this->merge(['slug' => Str::slug($this->slug)]); // Normaliser le slug s'il est fourni
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Le nom de la catégorie est obligatoire.'),
            'name.unique' => __('Ce nom de catégorie est déjà utilisé.'),
            'slug.unique' => __('Ce slug est déjà utilisé par une autre catégorie.'),
            'slug.alpha_dash' => __('Le slug ne peut contenir que des lettres, chiffres, tirets et underscores.'),
        ];
    }
}