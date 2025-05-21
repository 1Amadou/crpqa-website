<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;

class PublicEventController extends Controller
{
    /**
     * Affiche la liste des événements publics.
     */
    public function index()
    {
        $events = Event::whereNotNull('start_datetime')
                       ->where('start_datetime', '>=', now())
                       ->orderBy('start_datetime', 'asc')
                       ->paginate(10);

        return view('public.events.index', compact('events'));
    }

    /**
     * Affiche le détail d'un événement.
     *
     * @param  Event  $event
     */
    public function show(Event $event)
    {
        // Charger les inscriptions associées si nécessaire
        $registrations = $event->registrations()->where('status', 'confirmed')->get();

        return view('public.events.show', compact('event', 'registrations'));
    }

    /**
     * Gère l'inscription d'un utilisateur à un événement.
     */
    public function register(Request $request, Event $event)
    {
        // Validation et création de l'inscription
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $registration = new Registration($data);
        $registration->event()->associate($event);
        $registration->save();

        return redirect()->route('events.show', $event->slug)
                         ->with('success', 'Inscription réussie !');
    }
}
