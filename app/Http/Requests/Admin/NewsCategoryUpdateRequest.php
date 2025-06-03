<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class NewsCategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('manage news_categories');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('news_category')->id; // 'news_category' est le nom du paramètre de route

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('news_categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                Rule::unique('news_categories', 'slug')->ignore($categoryId),
            ],
            'color' => 'nullable|string|max:50',
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

        // Si le slug est vide et que le nom a changé (ou si slug était vide), le regénérer
        // Ou si un slug est fourni mais différent de l'actuel, le normaliser
        $category = $this->route('news_category');
        if (empty($this->slug) && !empty($this->name)) {
            if ($category && ($category->name !== $this->name || !$category->slug)) {
                 $this->merge(['slug' => Str::slug($this->name)]);
            }
        } elseif (!empty($this->slug)) {
            $this->merge(['slug' => Str::slug($this->slug)]);
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