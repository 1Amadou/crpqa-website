<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan; // Pour vider le cache de la config
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\StaticPage;

class SiteSettingController extends Controller
{
    public function __construct()
    {
        // Seuls les utilisateurs avec la permission 'manage site settings' peuvent accéder.
        $this->middleware(['permission:manage site settings']);
    }

    /**
     * Récupère la première (et unique) ligne de paramètres ou en crée une vide.
     * Cela garantit qu'il y a toujours un enregistrement avec lequel travailler.
     */
    private function getSettings(): SiteSetting
    {
        // Nous supposons un ID fixe de 1 pour la ligne unique des paramètres.
        // Ou utiliser firstOrCreate pour s'assurer qu'elle existe.
        return SiteSetting::firstOrCreate(['id' => 1]);
    }

    /**
     * Affiche le formulaire pour modifier les paramètres du site.
     */
    public function edit()
    {
        $settings = $this->getSettings();
        // Récupérer les pages statiques publiées pour les listes déroulantes
        $staticPages = StaticPage::where('is_published', true)
                                ->orderBy('title')
                                ->pluck('title', 'slug') // On utilise le titre pour l'affichage, le slug comme valeur
                                ->all();

        return view('admin.settings.edit', compact('settings', 'staticPages')); // << PASSER $staticPages À LA VUE
    }

    /**
     * Met à jour les paramètres du site dans la base de données.
     */
    public function update(Request $request)
    {
        $settings = $this->getSettings();

        $rules = [
            'site_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'maps_url' => 'nullable|url|max:500', // Assurez-vous que la clé du formulaire correspond à 'maps_url'
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'footer_text' => 'nullable|string|max:1000',

            'cookie_consent_enabled' => 'nullable|boolean', // Sera géré avec $request->boolean()
            'cookie_consent_message' => 'nullable|string|max:2000',
            'cookie_policy_url' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where(function ($query) {
                return $query->where('is_published', true);
            })],
            'privacy_policy_url' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where(function ($query) {
                return $query->where('is_published', true);
            })],
            'terms_of_service_url' => ['nullable', 'string', 'max:255', Rule::exists('static_pages', 'slug')->where(function ($query) {
                return $query->where('is_published', true);
            })],

            'default_sender_email' => 'nullable|email|max:255',
            'default_sender_name' => 'nullable|string|max:255',
            'google_analytics_id' => 'nullable|string|max:50',
            'maintenance_mode' => 'nullable|boolean', // Sera géré avec $request->boolean()
            'maintenance_message' => 'nullable|string|max:2000',
        ];

        $validatedData = $request->validate($rules);

        // Assignation des valeurs validées
        // Pour les champs qui ne sont pas des fichiers ou des booléens spéciaux
        $fieldsToUpdate = [
            'site_name', 'contact_email', 'contact_phone', 'address',
            'maps_url', // Vérifiez la casse de 'maps_url' ici et dans votre formulaire/migration.
            'facebook_url', 'twitter_url', 'linkedin_url', 'youtube_url',
            'footer_text', 'cookie_consent_message', 'cookie_policy_url',
            'privacy_policy_url', 'terms_of_service_url', 'default_sender_email',
            'default_sender_name', 'google_analytics_id', 'maintenance_message',
        ];

        foreach ($fieldsToUpdate as $field) {
            if (isset($validatedData[$field])) {
                $settings->$field = $validatedData[$field];
            } else {
                // Si le champ n'est pas dans validatedData (par ex. checkbox non cochée et pas de 'nullable|boolean' direct dans rules)
                // ou si on veut explicitement mettre à null si absent de la requête (pour les champs string/text nullable)
                // $settings->$field = null; // Ceci est déjà géré par $validatedData[$field] ?? null si le champ est nullable
            }
        }

        // Gestion des booléens (cases à cocher)
        $settings->cookie_consent_enabled = $request->boolean('cookie_consent_enabled');
        $settings->maintenance_mode = $request->boolean('maintenance_mode');

        // Gestion de l'upload du logo
        if ($request->hasFile('logo')) {
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $logoName = 'site_logo.' . $request->file('logo')->getClientOriginalExtension();
            $settings->logo_path = $request->file('logo')->storeAs('site', $logoName, 'public');
        } elseif ($request->boolean('remove_logo')) {
             if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = null;
        }

        // Gestion de l'upload du favicon
        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $faviconName = 'favicon.' . $request->file('favicon')->getClientOriginalExtension();
            $settings->favicon_path = $request->file('favicon')->storeAs('site', $faviconName, 'public');
        } elseif ($request->boolean('remove_favicon')) {
            if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $settings->favicon_path = null;
        }

        $settings->save();

        // Optionnel: Vider le cache de configuration si certains paramètres affectent la config globale
        // Artisan::call('config:cache'); // Peut être lourd, à utiliser avec discernement

        return redirect()->route('admin.settings.edit')
                         ->with('success', 'Paramètres du site mis à jour avec succès.');
    }
}