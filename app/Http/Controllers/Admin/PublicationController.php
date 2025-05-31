<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use App\Models\Researcher; // Assurez-vous que ce modèle existe et est correctement namespace
use App\Http\Requests\Admin\PublicationStoreRequest;
use App\Http\Requests\Admin\PublicationUpdateRequest;
use Illuminate\Http\Request; // Gardé pour la méthode update, même si PublicationUpdateRequest est utilisé
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Pour auth()->id()

class PublicationController extends Controller
{
    private function getAvailableLocales()
    {
        return config('app.available_locales', ['fr', 'en']);
    }

    private function getPublicationTypes()
    {
        // Ceci est un exemple, adaptez selon vos besoins réels (enum, config, etc.)
        return [
            'journal_article' => 'Article de Journal',
            'conference_paper' => 'Article de Conférence',
            'book_chapter' => 'Chapitre de Livre',
            'book' => 'Livre',
            'report' => 'Rapport',
            'thesis' => 'Thèse',
            'preprint' => 'Prépublication',
            'other' => 'Autre',
        ];
    }

    public function index()
    {
        $publications = Publication::with('createdBy')->latest()->paginate(15);
        return view('admin.publications.index', compact('publications'));
    }

    public function create()
    {
        $availableLocales = $this->getAvailableLocales();
        $users = User::orderBy('name')->pluck('name', 'id'); // Pour created_by_user_id
        $locale = app()->getLocale();
        $researchers = Researcher::orderBy('last_name_' . $locale)
                                 ->orderBy('first_name_' . $locale)
                                 ->get();
        $publicationTypes = $this->getPublicationTypes();
        return view('admin.publications.create', compact('availableLocales', 'users', 'researchers', 'publicationTypes'));
    }

    public function store(PublicationStoreRequest $request)
    {
        $validatedData = $request->validated();
        $defaultLocale = config('app.locale', 'fr');

        if (empty($validatedData['slug']) && !empty($validatedData['title_' . $defaultLocale])) {
            $slug = Str::slug($validatedData['title_' . $defaultLocale]);
            $originalSlug = $slug;
            $count = 1;
            while (Publication::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $validatedData['slug'] = $slug;
        }

        $validatedData['created_by_user_id'] = $validatedData['created_by_user_id'] ?? Auth::id();

        $publication = Publication::create($validatedData);

        if ($request->hasFile('publication_pdf')) {
            $publication->addMediaFromRequest('publication_pdf')->toMediaCollection('publication_pdf');
        }

        if ($request->has('researchers')) {
            $publication->researchers()->sync($request->input('researchers'));
        }

        return redirect()->route('admin.publications.index')->with('success', 'Publication créée avec succès.');
    }

    public function show(Publication $publication)
    {
        $publication->load('researchers', 'createdBy', 'media');
        $availableLocales = $this->getAvailableLocales();
        $publicationTypes = $this->getPublicationTypes();
        $publicationTypeDisplay = $publicationTypes[$publication->type] ?? $publication->type;

        return view('admin.publications.show', compact('publication', 'availableLocales', 'publicationTypeDisplay'));
    }

    public function edit(Publication $publication)
    {
        $publication->load('media', 'researchers');
        $availableLocales = $this->getAvailableLocales();
        $users = User::orderBy('name')->pluck('name', 'id');
        $locale = app()->getLocale();
        $researchers = Researcher::orderBy('last_name_' . $locale)
                                 ->orderBy('first_name_' . $locale)
                                 ->get();
        $publicationTypes = $this->getPublicationTypes();

        return view('admin.publications.edit', compact('publication', 'availableLocales', 'users', 'researchers', 'publicationTypes'));
    }

    public function update(PublicationUpdateRequest $request, Publication $publication)
    {
        $validatedData = $request->validated();
        $defaultLocale = config('app.locale', 'fr');

        if (empty($validatedData['slug']) && !empty($validatedData['title_' . $defaultLocale])) {
            if ($publication->getTranslation('title', $defaultLocale, false) !== $validatedData['title_' . $defaultLocale] || $publication->slug === null) {
                $slug = Str::slug($validatedData['title_' . $defaultLocale]);
                $originalSlug = $slug;
                $count = 1;
                while (Publication::where('slug', $slug)->where('id', '!=', $publication->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $validatedData['slug'] = $slug;
            }
        } elseif (!empty($validatedData['slug']) && $validatedData['slug'] !== $publication->slug) {
            $slug = $validatedData['slug'];
            $originalSlug = $slug;
            $count = 1;
            while (Publication::where('slug', $slug)->where('id', '!=', $publication->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $validatedData['slug'] = $slug;
        }


        $publication->update($validatedData);

        if ($request->hasFile('publication_pdf')) {
            $publication->clearMediaCollection('publication_pdf');
            $publication->addMediaFromRequest('publication_pdf')->toMediaCollection('publication_pdf');
        } elseif ($request->input('remove_publication_pdf') == 1) {
            $publication->clearMediaCollection('publication_pdf');
        }

        if ($request->has('researchers')) {
            $publication->researchers()->sync($request->input('researchers'));
        } else {
            // Si vous voulez que la non-sélection supprime toutes les associations
            // $publication->researchers()->detach();
        }

        return redirect()->route('admin.publications.index')->with('success', 'Publication mise à jour avec succès.');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();
        return redirect()->route('admin.publications.index')->with('success', 'Publication supprimée avec succès.');
    }
}