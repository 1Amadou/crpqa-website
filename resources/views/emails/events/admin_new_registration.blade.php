@component('mail::message')
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $siteName }} Logo" style="max-height: 70px; margin-bottom: 20px;">
@else
# Nouvelle Inscription - {{ $siteName }}
@endif

Une nouvelle inscription a été enregistrée pour l'événement : **{{ $eventName }}**.

**Détails du participant :**
- **Nom :** {{ $participantName }}
- **Email :** {{ $participantEmail }}
- **Date d'inscription :** {{ $registrationDate }}

Vous pouvez gérer cette inscription et les autres en cliquant sur le bouton ci-dessous :

@component('mail::button', ['url' => $eventAdminUrl, 'color' => 'primary'])
Gérer les Inscriptions
@endcomponent

Cordialement,
Le Système de Notification du {{ $siteName }}
@endcomponent