<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration; // Pour la méthode register
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Pour la méthode register
use Illuminate\Support\Facades\Validator; // Pour la méthode register

class PublicEventController extends Controller
{
    /**
     * Display a listing of upcoming and past events.
     */
    public function index(Request $request)
    {
        $now = now();
        $filter = $request->input('filter', 'upcoming'); // 'upcoming', 'past', or 'all'
        $searchTerm = $request->input('search', ''); // Définit une valeur par défaut vide
        $currentLocale = app()->getLocale(); // Récupère la langue actuelle de l'application
    
        $query = Event::query()->with(['media', 'partners']);
    
        // Appliquer le filtre sur les événements
        if ($filter === 'upcoming') {
            $query->where('start_datetime', '>=', $now)->orderBy('start_datetime', 'asc');
            $pageTitle = __('Événements à Venir');
        } elseif ($filter === 'past') {
            $query->where('start_datetime', '<', $now)->orderBy('start_datetime', 'desc');
            $pageTitle = __('Événements Passés');
        } else {
            $query->orderBy('start_datetime', 'desc');
            $pageTitle = __('Tous les Événements');
        }
    
        // Appliquer le filtre de recherche
        if (!empty($searchTerm)) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }
    
        $events = $query->paginate(9)->appends($request->query());
    
        return view('public.events.index', compact('events', 'pageTitle', 'filter', 'searchTerm', 'currentLocale'));
    }
    

    /**
     * Display the specified event.
     */
    public function show(Event $event) // Route Model Binding
    {
        // if (!$event->is_published && !(auth()->check() && auth()->user()->can('preview unpublished events'))) {
        //     abort(404);
        // }
        $event->load(['media', 'partners', 'registrations']); // Eager load relations

        $metaTitle = $event->meta_title ?: $event->title;
        $metaDescription = $event->meta_description ?: Str::limit(strip_tags($event->description), 160);
        $ogImage = $event->cover_image_url ?: (app('siteSettings')->default_og_image_url ?? null);

        // Événements similaires (par exemple, futurs événements non celui-ci)
        $relatedEvents = Event::where('id', '!=', $event->id)
            // ->where('is_published', true)
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime', 'asc')
            ->take(3)
            ->get();

        return view('public.events.show', compact('event', 'metaTitle', 'metaDescription', 'ogImage', 'relatedEvents'));
    }

    /**
     * Handle event registration.
     */
    public function register(Request $request, Event $event)
    {
        if (!$event || $event->start_datetime < now()) {
            return back()->with('error', __('Les inscriptions pour cet événement sont fermées ou l\'événement est passé.'));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'organization' => 'nullable|string|max:255',
            // Ajoutez d'autres champs si votre formulaire d'inscription en a
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error_modal_event_id', $event->id);
        }

        // Vérifier si l'email est déjà inscrit à cet événement
        $existingRegistration = EventRegistration::where('event_id', $event->id)
                                                ->where('email', $request->input('email'))
                                                ->first();
        if ($existingRegistration) {
            return back()->with('warning', __('Vous êtes déjà inscrit à cet événement avec cet email.'))->with('error_modal_event_id', $event->id);
        }

        try {
            EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(), // Peut être null si l'utilisateur n'est pas connecté
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'organization' => $request->input('organization'),
                'status' => 'pending', // Ou 'approved' directement si pas de modération
                'registered_at' => now(),
            ]);

            // Optionnel: Envoyer un email de confirmation à l'utilisateur et à l'admin
            // Mail::to($request->input('email'))->send(new EventRegistrationReceived($event, $request->all()));
            // Mail::to(config('mail.from.address'))->send(new AdminNewEventRegistration($event, $request->all()));

            return back()->with('success', __('Merci pour votre inscription ! Vous recevrez bientôt une confirmation.'))->with('success_modal_event_id', $event->id);

        } catch (\Exception $e) {
            // Log::error("Event registration error: " . $e->getMessage());
            return back()->with('error', __('Une erreur est survenue lors de votre inscription. Veuillez réessayer.'))->with('error_modal_event_id', $event->id);
        }
    }
}