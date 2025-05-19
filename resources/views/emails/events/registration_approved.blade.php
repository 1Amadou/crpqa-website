@component('mail::message')
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $siteName }} Logo" style="max-height: 70px; margin-bottom: 20px;">
@else
# {{ $siteName }}
@endif

Félicitations **{{ $participantName }}** !

Bonne nouvelle ! Votre inscription à l'événement **{{ $eventName }}** a été approuvée.

**Détails de l'événement :**
- **Date et Heure :** {{ $eventDate }}
@if($eventLocation)
- **Lieu :** {{ $eventLocation }}
@endif

Nous avons hâte de vous accueillir ! Vous pouvez consulter les détails de l'événement ici :
@component('mail::button', ['url' => $eventUrl, 'color' => 'success'])
Voir l'Événement
@endcomponent

Si vous avez des questions ou si vous ne pouvez plus participer, veuillez nous en informer.

Cordialement,
L'équipe de {{ $siteName }}
@endcomponent