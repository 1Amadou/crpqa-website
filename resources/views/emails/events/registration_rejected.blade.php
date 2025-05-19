@component('mail::message')
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $siteName }} Logo" style="max-height: 70px; margin-bottom: 20px;">
@else
# {{ $siteName }}
@endif

Cher/Chère **{{ $participantName }}**,

Nous vous contactons concernant votre inscription à l'événement : **{{ $eventName }}**.

Malheureusement, nous ne sommes pas en mesure d'approuver votre participation pour le moment.
{{-- Optionnel: ajouter une raison si elle est stockée dans $registration->notes et si 'rejectionReason' est passé --}}
{{-- @if(isset($rejectionReason) && $rejectionReason)
Raison : {{ $rejectionReason }}
@endif --}}

Nous vous remercions de votre intérêt pour nos événements.
Vous pouvez consulter les détails de l'événement ici :
@component('mail::button', ['url' => $eventUrl, 'color' => 'secondary'])
Voir l'Événement
@endcomponent

N'hésitez pas à nous contacter si vous avez des questions.

Cordialement,
L'équipe de {{ $siteName }}
@endcomponent