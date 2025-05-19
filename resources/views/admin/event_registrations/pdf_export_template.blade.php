<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Inscriptions - {{ $event->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; } /* DejaVu Sans pour les caractères spéciaux/accents */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; margin-bottom: 20px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Liste des Inscriptions pour l'Événement : {{ $event->title }}</h1>
    <p>Date d'export : {{ now()->format('d/m/Y H:i') }}</p>
    <p>Total des inscriptions listées : {{ $registrations->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Organisation</th>
                <th>Statut</th>
                <th>Inscrit le</th>
                <th>Utilisateur Lié</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registrations as $registration)
                <tr>
                    <td>{{ $registration->id }}</td>
                    <td>{{ $registration->name }}</td>
                    <td>{{ $registration->email }}</td>
                    <td>{{ $registration->phone_number ?? 'N/A' }}</td>
                    <td>{{ $registration->organization ?? 'N/A' }}</td>
                    <td>{{ $statuses[$registration->status] ?? ucfirst(str_replace('_', ' ', $registration->status)) }}</td>
                    <td>{{ $registration->registered_at ? $registration->registered_at->format('d/m/Y H:i') : '' }}</td>
                    <td>{{ $registration->user ? $registration->user->name . ' (ID: '.$registration->user_id.')' : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Aucune inscription à afficher.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>