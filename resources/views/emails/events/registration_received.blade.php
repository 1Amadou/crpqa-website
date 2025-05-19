@component('mail::message')
{{-- Utilisation du logo si disponible --}}
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $siteName }} Logo" style="max-height: 70px; margin-bottom: 20px;">
@else
# {{ $siteName }}
@endif

Cher/Chère **{{ $participantName }}**,

Nous vous remercions pour votre inscription à l'événement : **{{ $eventName }}**.

Votre demande d'inscription a bien été enregistrée le {{ $registrationDate }} et est actuellement en attente de validation par nos équipes.
Vous recevrez un nouvel e-mail une fois que votre statut d'inscription aura été mis à jour.

Vous pouvez consulter les détails de l'événement ici :
@component('mail::button', ['url' => $eventUrl, 'color' => 'primary'])
Voir l'Événement
@endcomponent

Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,
L'équipe de {{ $siteName }}
@endcomponent