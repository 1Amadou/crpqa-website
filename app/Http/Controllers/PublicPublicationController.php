<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicPublicationController extends Controller
{
    private function getPublicationTypes(): array
    {
        // Doit correspondre aux clés utilisées dans PublicationController (Admin) et potentiellement dans la factory/seeder
        return [
            'journal_article' => __('publications.types.journal_article'), // Exemple avec traduction
            'conference_paper' => __('publications.types.conference_paper'),
            'book_chapter' => __('publications.types.book_chapter'),
            'book' => __('publications.types.book'),
            'report' => __('publications.types.report'),
            'thesis' => __('publications.types.thesis'),
            'preprint' => __('publications.types.preprint'),
            'other' => __('publications.types.other'),
        ];
        // Si vous n'utilisez pas les fichiers de traduction, revenez à un tableau simple :
        // return [
        //     'journal_article' => 'Article de Journal',
        //     'conference_paper' => 'Article de Conférence',
        //     // ... etc.
        // ];
    }

    public function index()
    {
        $query = Publication::query();

        // VALIDER : Décommentez et utilisez si vous avez une colonne 'is_published'
        // if (Schema::hasColumn('publications', 'is_published')) {
        //     $query->where('is_published', true);
        // }

        $publications = $query->with(['media', 'researchers']) // Charger les médias (pour PDF) et les chercheurs
                               ->orderBy('publication_date', 'desc')
                               ->paginate(10);

        return view('public.publications.index', compact('publications'));
    }

    // Utilisation du Route Model Binding pour $publication (basé sur le slug)
    public function show(Publication $publication)
    {
        // VALIDER : Décommentez et utilisez si vous avez une colonne 'is_published'
        // if (Schema::hasColumn('publications', 'is_published') && !$publication->is_published) {
        //     abort(404); // Ne pas montrer les publications non publiées
        // }
        
        $publication->load(['researchers', 'createdBy', 'media']);

        // Le trait HasLocalizedFields devrait gérer l'affichage de $publication->title et $publication->abstract
        // dans la langue courante directement dans la vue.
        $metaTitle = $publication->title; // Accède au titre dans la locale courante
        $metaDescription = Str::limit(strip_tags($publication->abstract), 160); // Accède à l'abstract dans la locale courante

        $publicationTypes = $this->getPublicationTypes();
        $publicationTypeDisplay = $publicationTypes[$publication->type] ?? $publication->type;

        return view('public.publications.show', compact(
            'publication',
            'metaTitle',
            'metaDescription',
            'publicationTypeDisplay'
        ));
    }
}