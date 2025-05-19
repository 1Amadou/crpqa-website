<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Researcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Important pour la gestion des fichiers
use Illuminate\Support\Str; // Optionnel, pour générer des noms de fichiers uniques

class ResearcherController extends Controller
{
    /**
     * Helper function to define validation rules.
     * @param Researcher|null $researcher
     * @return array
     */
    private function validationRules(Researcher $researcher = null): array
    {
        $photoRule = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'; // 2MB Max
        if (!$researcher) { // For store method (creation)
            // $photoRule = 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'; // 'sometimes' if photo is optional on create
        }

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:researchers,email,' . ($researcher ? $researcher->id : 'NULL') . ',id',
            'phone_number' => 'nullable|string|max:50',
            'biography' => 'nullable|string',
            'photo' => $photoRule, // Validation rule for the uploaded photo file
            'research_areas' => 'nullable|string',
            'linkedin_url' => 'nullable|url|max:255',
            'google_scholar_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $researchers = Researcher::orderBy('display_order', 'asc')
                                ->orderBy('last_name', 'asc')
                                ->orderBy('first_name', 'asc')
                                ->paginate(15);
        return view('admin.researchers.index', compact('researchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.researchers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $researcher = new Researcher();
        $researcher->first_name = $validatedData['first_name'];
        $researcher->last_name = $validatedData['last_name'];
        $researcher->title = $validatedData['title'];
        $researcher->position = $validatedData['position'];
        $researcher->email = $validatedData['email'];
        $researcher->phone_number = $validatedData['phone_number'];
        $researcher->biography = $validatedData['biography'];
        $researcher->research_areas = $validatedData['research_areas'];
        $researcher->linkedin_url = $validatedData['linkedin_url'];
        $researcher->google_scholar_url = $validatedData['google_scholar_url'];
        $researcher->is_active = $request->boolean('is_active');
        $researcher->display_order = $validatedData['display_order'] ?? 0;

        if ($request->hasFile('photo')) {
            // Générer un nom de fichier unique pour éviter les conflits
            $fileName = Str::slug($validatedData['last_name'] . '-' . $validatedData['first_name']) . '-' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            // Stocker le fichier dans 'storage/app/public/researcher_photos'
            // Assurez-vous d'avoir exécuté `php artisan storage:link`
            $path = $request->file('photo')->storeAs('researcher_photos', $fileName, 'public');
            $researcher->photo_path = $path;
        }

        $researcher->save();

        return redirect()->route('admin.researchers.index')
                         ->with('success', 'Profil du chercheur "' . $researcher->first_name . ' ' . $researcher->last_name . '" créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Researcher $researcher)
    {
        return view('admin.researchers.show', compact('researcher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Researcher $researcher)
    {
        return view('admin.researchers.edit', compact('researcher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Researcher $researcher)
    {
        $validatedData = $request->validate($this->validationRules($researcher));

        $researcher->first_name = $validatedData['first_name'];
        $researcher->last_name = $validatedData['last_name'];
        $researcher->title = $validatedData['title'];
        $researcher->position = $validatedData['position'];
        $researcher->email = $validatedData['email'];
        $researcher->phone_number = $validatedData['phone_number'];
        $researcher->biography = $validatedData['biography'];
        $researcher->research_areas = $validatedData['research_areas'];
        $researcher->linkedin_url = $validatedData['linkedin_url'];
        $researcher->google_scholar_url = $validatedData['google_scholar_url'];
        $researcher->is_active = $request->boolean('is_active');
        $researcher->display_order = $validatedData['display_order'] ?? 0;

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($researcher->photo_path && Storage::disk('public')->exists($researcher->photo_path)) {
                Storage::disk('public')->delete($researcher->photo_path);
            }
            // Stocker la nouvelle photo
            $fileName = Str::slug($validatedData['last_name'] . '-' . $validatedData['first_name']) . '-' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('researcher_photos', $fileName, 'public');
            $researcher->photo_path = $path;
        } elseif ($request->boolean('remove_photo')) { // Si une case "supprimer photo" est cochée
            if ($researcher->photo_path && Storage::disk('public')->exists($researcher->photo_path)) {
                Storage::disk('public')->delete($researcher->photo_path);
            }
            $researcher->photo_path = null;
        }


        $researcher->save();

        return redirect()->route('admin.researchers.index')
                         ->with('success', 'Profil du chercheur "' . $researcher->first_name . ' ' . $researcher->last_name . '" mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Researcher $researcher)
    {
        $researcherName = $researcher->first_name . ' ' . $researcher->last_name;

        // Supprimer la photo associée si elle existe
        if ($researcher->photo_path && Storage::disk('public')->exists($researcher->photo_path)) {
            Storage::disk('public')->delete($researcher->photo_path);
        }

        $researcher->delete();

        return redirect()->route('admin.researchers.index')
                         ->with('success', 'Profil du chercheur "' . $researcherName . '" supprimé avec succès.');
    }
}