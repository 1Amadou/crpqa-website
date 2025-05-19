<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResearchAxis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResearchAxisController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['permission:manage research axes']);
    }

    private function validationRules(ResearchAxis $researchAxis = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('research_axes', 'slug')->ignore($researchAxis?->id)],
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'icon_class' => 'nullable|string|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ];
    }

    public function index()
    {
        $researchAxes = ResearchAxis::orderBy('display_order')->orderBy('name')->paginate(15);
        return view('admin.research_axes.index', compact('researchAxes'));
    }

    public function create()
    {
        return view('admin.research_axes.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $researchAxis = new ResearchAxis();
        $researchAxis->fill($validatedData);
        $researchAxis->is_active = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            $fileName = Str::slug($validatedData['name']) . '-axis-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = $request->file('cover_image')->storeAs('research_axis_covers', $fileName, 'public');
            $researchAxis->cover_image_path = $path;
        }

        $researchAxis->save();

        return redirect()->route('admin.research-axes.index')
                         ->with('success', 'Domaine de recherche "' . $researchAxis->name . '" créé avec succès.');
    }

    public function show(ResearchAxis $researchAxis)
    {
        return view('admin.research_axes.show', compact('researchAxis'));
    }

    public function edit(ResearchAxis $researchAxis)
    {
        return view('admin.research_axes.edit', compact('researchAxis'));
    }

    public function update(Request $request, ResearchAxis $researchAxis)
    {
        $validatedData = $request->validate($this->validationRules($researchAxis));

        $researchAxis->fill($validatedData);
        $researchAxis->is_active = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            if ($researchAxis->cover_image_path && Storage::disk('public')->exists($researchAxis->cover_image_path)) {
                Storage::disk('public')->delete($researchAxis->cover_image_path);
            }
            $fileName = Str::slug($validatedData['name']) . '-axis-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = $request->file('cover_image')->storeAs('research_axis_covers', $fileName, 'public');
            $researchAxis->cover_image_path = $path;
        } elseif ($request->boolean('remove_cover_image')) {
            if ($researchAxis->cover_image_path && Storage::disk('public')->exists($researchAxis->cover_image_path)) {
                Storage::disk('public')->delete($researchAxis->cover_image_path);
            }
            $researchAxis->cover_image_path = null;
        }

        $researchAxis->save();

        return redirect()->route('admin.research-axes.index')
                         ->with('success', 'Domaine de recherche "' . $researchAxis->name . '" mis à jour avec succès.');
    }

    public function destroy(ResearchAxis $researchAxis)
    {
        $researchAxisName = $researchAxis->name;

        if ($researchAxis->cover_image_path && Storage::disk('public')->exists($researchAxis->cover_image_path)) {
            Storage::disk('public')->delete($researchAxis->cover_image_path);
        }

        $researchAxis->delete();

        return redirect()->route('admin.research-axes.index')
                         ->with('success', 'Domaine de recherche "' . $researchAxisName . '" supprimé avec succès.');
    }
}
