<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Partner;
use App\Models\User; // Importé pour Auth::id() et potentiellement lister des auteurs
// Pour une meilleure pratique, déplacez les règles de validation dans ces Form Requests :
// use App\Http\Requests\Admin\EventStoreRequest;
// use App\Http\Requests\Admin\EventUpdateRequest;
use Illuminate\Http\Request; // À remplacer par les FormRequests spécifiques
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EventController extends Controller
{
    protected array $availableLocales;

    public function __construct()
{
    // Appliquer les permissions aux méthodes spécifiques du Ressource Controller
    // $this->middleware(['permission:view events'])->only(['index', 'show']);
    // $this->middleware(['permission:create events'])->only(['create', 'store']);
    // $this->middleware(['permission:edit events'])->only(['edit', 'update']);
    // $this->middleware(['permission:delete events'])->only(['destroy']);
    // $this->middleware(['permission:publish events'])->only(['publish', 'unpublish']); // Si vous avez ces méthodes

    // Assurez-vous que toutes les méthodes sont couvertes ou ajoutez une permission générale d'accès au module
    // Si vous voulez un accès général au module pour certaines actions non couvertes ci-dessus :
    // $this->middleware(['permission:access events module']); // Et créez cette permission

    $this->availableLocales = config('app.available_locales', ['fr', 'en']);
}

    // Déplacer validationRules dans EventStoreRequest & EventUpdateRequest
    private function validationRules(Event $event = null): array
    {
        $primaryLocale = $this->availableLocales[0] ?? config('app.locale', 'fr');
        $rules = [
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash:ascii',
                $event ? Rule::unique('events', 'slug')->ignore($event->id) : 'unique:events,slug',
            ],
            'start_datetime_date' => 'required|date_format:Y-m-d',
            'start_datetime_time' => 'required|date_format:H:i',
            'end_datetime_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_datetime_date',
            'end_datetime_time' => 'nullable|date_format:H:i', // Doit être validé avec end_datetime_date si présent
            'registration_url' => 'nullable|url|max:255',
            'is_featured' => 'boolean',
            'partner_ids' => 'nullable|array',
            'partner_ids.*' => 'exists:partners,id',
            'event_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048', // Pour Spatie
            'remove_event_cover_image' => 'nullable|boolean',
        ];

        foreach ($this->availableLocales as $locale) {
            $rules['title_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string|max:255';
            $rules['description_' . $locale] = ($locale === $primaryLocale ? 'required' : 'nullable') . '|string';
            $rules['location_' . $locale] = 'nullable|string|max:255';
            $rules['meta_title_' . $locale] = 'nullable|string|max:255';
            $rules['meta_description_' . $locale] = 'nullable|string|max:1000';
            $rules['target_audience_' . $locale] = 'nullable|string|max:5000';
            $rules['cover_image_alt_' . $locale] = 'nullable|string|max:255';
        }
        return $rules;
    }

    private function combineDateTime(?string $date, ?string $time): ?Carbon
    {
        if ($date && $time) {
            return Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        } elseif ($date) {
            return Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        }
        return null;
    }

    public function index()
    {
        $events = Event::with('createdBy') // Utiliser createdBy pour la relation auteur
            ->orderBy('start_datetime', 'desc')
            ->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $event = new Event(['is_featured' => false]); // Pré-remplir des valeurs par défaut
        $availableLocales = $this->availableLocales;
        $partners = Partner::where('is_active', true)->orderBy('name_'.app()->getLocale())->get()->pluck('name', 'id'); // Traduction du nom si Partner utilise HasLocalizedFields

        return view('admin.events.create', compact('event', 'availableLocales', 'partners'));
    }

    public function store(Request $request) // Remplacer par EventStoreRequest $request
    {
        $validatedData = $request->validate($this->validationRules());
        $primaryLocale = config('app.locale', 'fr');

        $eventData = [];
        foreach ($this->availableLocales as $locale) {
            $eventData['title_' . $locale] = $validatedData['title_' . $locale] ?? null;
            $eventData['description_' . $locale] = $validatedData['description_' . $locale] ?? null;
            $eventData['location_' . $locale] = $validatedData['location_' . $locale] ?? null;
            $eventData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
            $eventData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['description_' . $locale] ?? ''), 160);
            $eventData['target_audience_' . $locale] = $validatedData['target_audience_' . $locale] ?? null;
            $eventData['cover_image_alt_' . $locale] = $validatedData['cover_image_alt_' . $locale] ?? $validatedData['title_' . $locale] ?? null;
        }
        
        $titleForSlug = $validatedData['title_' . $primaryLocale] ?? 'evenement-' . time();
        if (empty($validatedData['slug'])) {
            $slug = Str::slug($titleForSlug);
            $originalSlug = $slug;
            $count = 1;
            while (Event::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $eventData['slug'] = $slug;
        } else {
            $eventData['slug'] = Str::slug($validatedData['slug']);
        }

        $eventData['start_datetime'] = $this->combineDateTime($validatedData['start_datetime_date'], $validatedData['start_datetime_time']);
        $eventData['end_datetime'] = $this->combineDateTime($request->input('end_datetime_date'), $request->input('end_datetime_time'));
        $eventData['registration_url'] = $validatedData['registration_url'] ?? null;
        $eventData['is_featured'] = $request->boolean('is_featured');
        $eventData['created_by_user_id'] = Auth::id();
        
        $event = Event::create($eventData);

        if ($request->hasFile('event_cover_image')) {
            $event->addMediaFromRequest('event_cover_image')->toMediaCollection('event_cover_image');
        }

        if ($request->filled('partner_ids')) {
            $event->partners()->sync($validatedData['partner_ids']);
        } else {
            $event->partners()->sync([]);
        }
        
        $displayTitle = $event->getTranslation('title', $primaryLocale, false) ?: $event->slug;
        return redirect()->route('admin.events.index')
                         ->with('success', "L'événement \"{$displayTitle}\" a été créé.");
    }

    public function show(Event $event)
    {
        $event->load(['createdBy', 'partners', 'media', 'registrations']); // Charger les relations
        $availableLocales = $this->availableLocales;
        return view('admin.events.show', compact('event', 'availableLocales'));
    }

    public function edit(Event $event)
    {
        $event->load(['partners', 'media']);
        $availableLocales = $this->availableLocales;
        $partners = Partner::where('is_active', true)->orderBy('name_'.app()->getLocale())->get()->pluck('name', 'id');

        return view('admin.events.edit', compact('event', 'availableLocales', 'partners'));
    }

    public function update(Request $request, Event $event) // Remplacer par EventUpdateRequest $request
    {
        $validatedData = $request->validate($this->validationRules($event));
        $primaryLocale = config('app.locale', 'fr');

        $updateData = [];
        foreach ($this->availableLocales as $locale) {
            if ($request->filled('title_' . $locale)) $updateData['title_' . $locale] = $validatedData['title_' . $locale];
            if ($request->filled('description_' . $locale)) $updateData['description_' . $locale] = $validatedData['description_' . $locale];
            if ($request->filled('location_' . $locale)) $updateData['location_' . $locale] = $validatedData['location_' . $locale];
            
            $updateData['meta_title_' . $locale] = $validatedData['meta_title_' . $locale] ?? $validatedData['title_' . $locale] ?? $event->getTranslation('title', $locale, false);
            $updateData['meta_description_' . $locale] = $validatedData['meta_description_' . $locale] ?? Str::limit(strip_tags($validatedData['description_' . $locale] ?? $event->getTranslation('description', $locale, false)), 160);
            $updateData['target_audience_' . $locale] = $validatedData['target_audience_' . $locale] ?? null;
            $updateData['cover_image_alt_' . $locale] = $validatedData['cover_image_alt_' . $locale] ?? $validatedData['title_' . $locale] ?? $event->getTranslation('title', $locale, false);
        }

        $currentTitleDefaultLocale = $event->getTranslation('title', $primaryLocale, false);
        $newTitleDefaultLocale = $validatedData['title_' . $primaryLocale] ?? $currentTitleDefaultLocale;

        if (empty($validatedData['slug'])) {
            if ($currentTitleDefaultLocale !== $newTitleDefaultLocale || !$event->slug) {
                $slug = Str::slug($newTitleDefaultLocale);
                $originalSlug = $slug;
                $count = 1;
                while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $updateData['slug'] = $slug;
            }
        } elseif ($validatedData['slug'] !== $event->slug) {
            $slug = Str::slug($validatedData['slug']);
            $originalSlug = $slug;
            $count = 1;
            while (Event::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $updateData['slug'] = $slug;
        }

        $updateData['start_datetime'] = $this->combineDateTime($validatedData['start_datetime_date'], $validatedData['start_datetime_time']);
        $updateData['end_datetime'] = $this->combineDateTime($request->input('end_datetime_date'), $request->input('end_datetime_time'));
        $updateData['registration_url'] = $validatedData['registration_url'] ?? null;
        $updateData['is_featured'] = $request->boolean('is_featured');
        // created_by_user_id n'est généralement pas mis à jour

        $event->update($updateData);

        if ($request->hasFile('event_cover_image')) {
            $event->clearMediaCollection('event_cover_image');
            $event->addMediaFromRequest('event_cover_image')->toMediaCollection('event_cover_image');
        } elseif ($request->boolean('remove_event_cover_image')) {
            $event->clearMediaCollection('event_cover_image');
        }

        if ($request->filled('partner_ids')) {
            $event->partners()->sync($validatedData['partner_ids']);
        } else {
            $event->partners()->sync([]);
        }

        $displayTitle = $event->getTranslation('title', $primaryLocale, false) ?: $event->slug;
        return redirect()->route('admin.events.index')
                         ->with('success', "L'événement \"{$displayTitle}\" a été mis à jour.");
    }

    public function destroy(Event $event)
    {
        $primaryLocale = config('app.locale', 'fr');
        $displayTitle = $event->getTranslation('title', $primaryLocale, false) ?: $event->slug;

        $event->clearMediaCollection('event_cover_image'); // Supprimer le média associé
        $event->partners()->detach(); // Détacher les partenaires
        $event->delete();

        return redirect()->route('admin.events.index')
                         ->with('success', "L'événement \"{$displayTitle}\" et ses médias associés ont été supprimés.");
    }
}