<x-mail::message>
# Nouveau Message depuis le Formulaire de Contact du Site CRPQA

Vous avez reçu un nouveau message :

**Nom :** {{ $name }}
**Email :** [{{ $email }}](mailto:{{ $email }})
**Sujet :** {{ $mailSubject }}

---

**Message :**
<x-mail::panel>
{{ $messageContent }}
</x-mail::panel>

---

Merci,<br>
{{ config('app.name') }}
</x-mail::message>