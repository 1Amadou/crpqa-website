<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Bien que non utilisé directement ici pour le slug, utile pour les noms de fichiers
use Illuminate\Validation\Rule;

class PartnerController extends Controller
{
    private function validationRules(Partner $partner = null): array
    {
        $logoRules = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'; // 2MB Max

        if ($partner && $partner->logo_path) { // Si update et image existe déjà
            $logoRules = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        } elseif (!$partner) { // Si create
            // Optionnel: rendre le logo requis à la création si vous le souhaitez
            // $logoRules = 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }

        return [
            'name' => 'required|string|max:255',
            'logo' => $logoRules, // Note: le champ dans le formulaire sera 'logo'
            'website_url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function index()
    {
        $partners = Partner::orderBy('display_order', 'asc')->orderBy('name', 'asc')->paginate(15);
        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $partner = new Partner();
        $partner->name = $validatedData['name'];
        $partner->website_url = $validatedData['website_url'] ?? null;
        $partner->description = $validatedData['description'] ?? null;
        $partner->type = $validatedData['type'] ?? null;
        $partner->display_order = $validatedData['display_order'] ?? 0;
        $partner->is_active = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $fileName = Str::slug($validatedData['name']) . '-' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $path = $request->file('logo')->storeAs('partner_logos', $fileName, 'public');
            $partner->logo_path = $path;
        }

        $partner->save();

        return redirect()->route('admin.partners.index')
                         ->with('success', 'Partenaire "' . $partner->name . '" créé avec succès.');
    }

    public function show(Partner $partner)
    {
        // Pour les partenaires, la vue 'show' est souvent simple ou parfois omise.
        // Si vous décidez d'avoir une vue 'show' détaillée :
        return view('admin.partners.show', compact('partner'));
        // Sinon, redirigez vers l'édition ou la liste :
        // return redirect()->route('admin.partners.edit', $partner);
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validatedData = $request->validate($this->validationRules($partner));

        $partner->name = $validatedData['name'];
        $partner->website_url = $validatedData['website_url'] ?? null;
        $partner->description = $validatedData['description'] ?? null;
        $partner->type = $validatedData['type'] ?? null;
        $partner->display_order = $validatedData['display_order'] ?? 0;
        $partner->is_active = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            if ($partner->logo_path && Storage::disk('public')->exists($partner->logo_path)) {
                Storage::disk('public')->delete($partner->logo_path);
            }
            $fileName = Str::slug($validatedData['name']) . '-' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $path = $request->file('logo')->storeAs('partner_logos', $fileName, 'public');
            $partner->logo_path = $path;
        } elseif ($request->boolean('remove_logo')) {
            if ($partner->logo_path && Storage::disk('public')->exists($partner->logo_path)) {
                Storage::disk('public')->delete($partner->logo_path);
            }
            $partner->logo_path = null;
        }

        $partner->save();

        return redirect()->route('admin.partners.index')
                         ->with('success', 'Partenaire "' . $partner->name . '" mis à jour avec succès.');
    }

    public function destroy(Partner $partner)
    {
        $partnerName = $partner->name;

        if ($partner->logo_path && Storage::disk('public')->exists($partner->logo_path)) {
            Storage::disk('public')->delete($partner->logo_path);
        }

        $partner->delete();

        return redirect()->route('admin.partners.index')
                         ->with('success', 'Partenaire "' . $partnerName . '" supprimé avec succès.');
    }
}