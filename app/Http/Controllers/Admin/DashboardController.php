<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News; // Assurez-vous que le namespace est correct pour News
use App\Models\Event;
use App\Models\Publication;
use App\Models\Researcher;
use App\Models\EventRegistration; // Pour le compte des inscriptions
use Carbon\Carbon; // Pour la gestion des dates

class DashboardController extends Controller
{
    public function __construct()
    {
        // Le dashboard est généralement accessible à tous les utilisateurs admin connectés
        // Si vous avez des permissions spécifiques pour voir le dashboard, ajoutez-les ici.
        // Exemple: $this->middleware(['permission:access admin panel']);
    }

    public function index()
    {
        $stats = [
            'researchers_count' => Researcher::where('is_active', true)->count(),
            'publications_count' => Publication::count(), // Peut-être filtrer par 'is_published' si ce champ existe
            'news_count' => News::whereNotNull('published_at')->where('published_at', '<=', now())->count(), // Actualités publiées
            'upcoming_events_count' => Event::where('start_datetime', '>=', now())->count(),
            'total_registrations_count' => EventRegistration::whereIn('status', ['approved', 'pending'])->count(),
        ];

        $latestNews = News::whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->orderBy('published_at', 'desc')
                            ->take(5)
                            ->get();

        $upcomingEvents = Event::where('start_datetime', '>=', now())
                               ->orderBy('start_datetime', 'asc')
                               ->take(5)
                               ->get();

        $latestPublications = Publication::orderBy('publication_date', 'desc') // Ou 'created_at' si plus pertinent pour "récent"
                                       ->take(5)
                                       ->get();

        // Message de bienvenue
        $welcomeMessage = "Bienvenue sur le panneau d'administration du CRPQA !";
        // Vous pourriez personnaliser ce message en fonction de l'heure ou de l'utilisateur.
        $currentHour = Carbon::now()->hour;
        if ($currentHour < 12) {
            $greeting = "Bonjour";
        } elseif ($currentHour < 18) {
            $greeting = "Bon après-midi";
        } else {
            $greeting = "Bonsoir";
        }
        $welcomeMessage = $greeting . ", " . auth()->user()->name . "! " . $welcomeMessage;


        return view('admin.dashboard', compact(
            'stats',
            'latestNews',
            'upcomingEvents',
            'latestPublications',
            'welcomeMessage'
        ));
    }
}