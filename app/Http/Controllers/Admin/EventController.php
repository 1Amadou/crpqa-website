<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Partner;
class EventController extends Controller
{
    /**
     * Helper function to define validation rules.
     * @param Event|null $event
     * @return array
     */
    private function validationRules(Event $event = null): array
    {
        $imageRule = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'; // 2MB Max

        return [
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('events')->ignore($event ? $event->id : null)],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'description' => 'required|string',
            'start_datetime_date' => 'required|date_format:Y-m-d',
            'start_datetime_time' => 'required|date_format:H:i',
            'end_datetime_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_datetime_date',
            'end_datetime_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'cover_image' => $imageRule,
            'registration_url' => 'nullable|url|max:255',
            'is_featured' => 'nullable|boolean',
            'target_audience' => 'nullable|string|max:2000',
            'partner_ids' => 'nullable|array', 
            'partner_ids.*' => 'exists:partners,id',
        ];
    }

    /**
     * Helper function to combine date and time.
     * @param string|null $date
     * @param string|null $time
     * @return Carbon|null
     */
    private function combineDateTime(?string $date, ?string $time): ?Carbon
    {
        if ($date && $time) {
            return Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        } elseif ($date) { // If only date is provided, default time to 00:00
            return Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        }
        return null;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('user')
                       ->orderBy('start_datetime', 'desc')
                       ->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('admin.events.create');

        $partners = Partner::where('is_active', true)->orderBy('name')->get(); // Récupérer les partenaires actifs
        return view('admin.events.create', compact('partners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $event = new Event();
        $event->title = $validatedData['title'];
        $event->slug = $validatedData['slug'];
        $event->description = $validatedData['description'];
        $event->target_audience = $validatedData['target_audience'] ?? null;
        
        $event->start_datetime = $this->combineDateTime($validatedData['start_datetime_date'], $validatedData['start_datetime_time']);
        $event->end_datetime = $this->combineDateTime($request->input('end_datetime_date'), $request->input('end_datetime_time'));

        $event->location = $validatedData['location'];
        $event->registration_url = $validatedData['registration_url'];
        $event->is_featured = $request->boolean('is_featured');
        $event->user_id = Auth::id();

        // Meta fields with defaults
        $event->meta_title = $validatedData['meta_title'] ?? Str::limit(strip_tags($validatedData['title']), 70, '');
        $event->meta_description = $validatedData['meta_description'] ?? Str::limit(strip_tags($validatedData['description']), 160, '...');


        if ($request->hasFile('cover_image')) {
            $fileName = Str::slug($validatedData['title']) . '-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = $request->file('cover_image')->storeAs('event_covers', $fileName, 'public');
            $event->cover_image_path = $path;
        }

        $event->save();

        if ($request->has('partner_ids')) {
            $event->associatedPartners()->sync($request->input('partner_ids'));
        } else {
            $event->associatedPartners()->sync([]); 
        }

        return redirect()->route('admin.events.index')
                         ->with('success', 'Événement "' . $event->title . '" créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user');
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // return view('admin.events.edit', compact('event'));

        $partners = Partner::where('is_active', true)->orderBy('name')->get();
        $event->load('associatedPartners'); // Charger les partenaires déjà associés pour la pré-sélection
        return view('admin.events.edit', compact('event', 'partners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validatedData = $request->validate($this->validationRules($event));

        $event->title = $validatedData['title'];
        $event->slug = $validatedData['slug'];
        $event->description = $validatedData['description'];
        $event->target_audience = $validatedData['target_audience'] ?? null;

        $event->start_datetime = $this->combineDateTime($validatedData['start_datetime_date'], $validatedData['start_datetime_time']);
        $event->end_datetime = $this->combineDateTime($request->input('end_datetime_date'), $request->input('end_datetime_time'));
        
        $event->location = $validatedData['location'];
        $event->registration_url = $validatedData['registration_url'];
        $event->is_featured = $request->boolean('is_featured');
        // user_id (auteur original) n'est généralement pas modifié

        $event->meta_title = $validatedData['meta_title'] ?? Str::limit(strip_tags($validatedData['title']), 70, '');
        $event->meta_description = $validatedData['meta_description'] ?? Str::limit(strip_tags($validatedData['description']), 160, '...');

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path)) {
                Storage::disk('public')->delete($event->cover_image_path);
            }
            $fileName = Str::slug($validatedData['title']) . '-' . time() . '.' . $request->file('cover_image')->getClientOriginalExtension();
            $path = $request->file('cover_image')->storeAs('event_covers', $fileName, 'public');
            $event->cover_image_path = $path;
        } elseif ($request->boolean('remove_cover_image')) {
            if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path)) {
                Storage::disk('public')->delete($event->cover_image_path);
            }
            $event->cover_image_path = null;
        }

        $event->save();

        if ($request->has('partner_ids')) {
            $event->associatedPartners()->sync($request->input('partner_ids'));
        } else {
            $event->associatedPartners()->sync([]);
        }

        return redirect()->route('admin.events.index')
                         ->with('success', 'Événement "' . $event->title . '" mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $eventTitle = $event->title;

        if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path)) {
            Storage::disk('public')->delete($event->cover_image_path);
        }
        
        $event->delete();

        return redirect()->route('admin.events.index')
                         ->with('success', 'Événement "' . $eventTitle . '" supprimé avec succès.');
    }

    
}