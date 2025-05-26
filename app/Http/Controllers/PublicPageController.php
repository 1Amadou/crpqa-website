<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\News;
use App\Models\Publication;
use App\Models\ResearchAxis;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;


class PublicPageController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        $locale = App::getLocale();

        // Actualités Récentes
        $latestNews = News::where('is_published', true)       // is_published existe pour News
            ->orderBy('published_at', 'desc')    // Tri par 'published_at'
            ->take(3)
            ->get();

        // Événements à Venir
        // La table Events n'a pas 'is_published', on filtre par date. Colonnes: start_datetime, end_datetime
        $upcomingEvents = Event::where('end_datetime', '>=', now())
            ->orderBy('start_datetime', 'asc')
            ->take(3)
            ->get();

        // Domaines de Recherche Clés
        // La table ResearchAxes a 'is_active' et 'display_order'
        $featuredResearchAxes = ResearchAxis::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->take(4)
            ->get();

        // Publications Récentes/En Vedette
        // La table Publications n'a PAS 'is_published', mais a 'is_featured' et 'publication_date'
        $featuredPublications = Publication::where('is_featured', true) // CORRIGÉ: Utilisation de 'is_featured'
            ->orderBy('publication_date', 'desc')
            ->take(4)
            ->get();

        // Contenu pour la "Brève Présentation du CRPQA"
        // La table static_pages a 'is_published'
        $presentationCrpqaPage = StaticPage::where('slug', 'presentation-crpqa')
            ->where('is_published', true) // Ajout du filtre is_published
            ->first();
        $presentationCrpqaContent = $presentationCrpqaPage ? $presentationCrpqaPage->getTranslation('content', $locale) : null;

        // Contenu pour "Appel à la Collaboration/Partenariat"
        $collaborationPage = StaticPage::where('slug', 'appel-collaboration')
            ->where('is_published', true) // Ajout du filtre is_published
            ->first();
        $collaborationContent = $collaborationPage ? $collaborationPage->getTranslation('content', $locale) : null;

        $testimonials = []; // Remplacer par la logique de récupération si applicable

        return view('public.home', compact(
            'latestNews',
            'upcomingEvents',
            'featuredResearchAxes',
            'featuredPublications',
            'presentationCrpqaContent',
            'collaborationContent',
            'testimonials'
        ));
    }

    /**
     * Display the about page.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        $locale = App::getLocale();
        // La table static_pages a 'is_published'
        $page = StaticPage::where('slug', 'a-propos')->where('is_published', true)->firstOrFail();
        $title = $page->getTranslation('title', $locale);
        $content = $page->getTranslation('content', $locale);
        $metaTitle = $page->getTranslation('seo_meta_title', $locale) ?: $title;
        $metaDescription = $page->getTranslation('seo_meta_description', $locale);

        return view('public.about', compact('page', 'title', 'content', 'metaTitle', 'metaDescription'));
    }

    /**
     * Display a generic static page.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function page(string $slug)
    {
        $locale = App::getLocale();
        // La table static_pages a 'is_published'
        $page = StaticPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $title = $page->getTranslation('title', $locale);
        $content = $page->getTranslation('content', $locale);
        $metaTitle = $page->getTranslation('seo_meta_title', $locale) ?: $title;
        $metaDescription = $page->getTranslation('seo_meta_description', $locale);

        if (View::exists("public.pages.{$slug}")) {
            return view("public.pages.{$slug}", compact('page', 'title', 'content', 'metaTitle', 'metaDescription'));
        }

        return view('public.static-page', compact('page', 'title', 'content', 'metaTitle', 'metaDescription'));
    }
}