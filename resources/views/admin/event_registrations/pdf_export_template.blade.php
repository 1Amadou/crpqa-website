<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Liste des Inscriptions') }} - {{ $event->getTranslation('title', app()->getLocale(), false) }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f0f0f0; font-weight: bold; }
        h1 { text-align: center; margin-bottom: 15px; font-size: 16px; color: #222; }
        .header-info p { margin: 2px 0; font-size: 10px; }
        .footer { text-align: center; font-size: 8px; position: fixed; bottom: 0px; width:100%; }
        .page-break { page-break-after: always; }
        .status-pending { background-color: #fff3cd; }
        .status-approved { background-color: #d4edda; }
        .status-rejected { background-color: #f8d7da; }
        .status-cancelled_by_user { background-color: #e2e3e5; }
        .status-attended { background-color: #cce5ff; }
    </style>
</head>
<body>
    <div class="header-info">
        <p style="text-align: right;">{{ __('Exporté le:') }} {{ now()->translatedFormat('d F Y \à H:i') }}</p>
        <h1>{{ __('Liste des Inscriptions pour l\'Événement :') }}<br>{{ $event->getTranslation('title', app()->getLocale(), false) }}</h1>
        <p><strong>{{ __('Date de l\'événement:') }}</strong> {{ $event->start_datetime->translatedFormat('d F Y') }}
            @if($event->end_datetime && $event->end_datetime->notEqualTo($event->start_datetime))
                {{ __('au') }} {{ $event->end_datetime->translatedFormat('d F Y') }}
            @endif
        </p>
        <p><strong>{{ __('Total des inscriptions listées:') }}</strong> {{ $registrations->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Nom') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Téléphone') }}</th>
                <th>{{ __('Organisation') }}</th>
                <th>{{ __('Statut') }}</th>
                <th>{{ __('Inscrit le') }}</th>
                <th>{{ __('Utilisateur Lié') }}</th>
                <th>{{ __('Notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registrations as $index => $registration)
                @php
                    $statusClass = '';
                    if(isset($registration->status)) {
                        switch ($registration->status) {
                            case 'pending': $statusClass = 'status-pending'; break;
                            case 'approved': $statusClass = 'status-approved'; break;
                            case 'rejected': $statusClass = 'status-rejected'; break;
                            case 'cancelled_by_user': $statusClass = 'status-cancelled_by_user'; break;
                            case 'attended': $statusClass = 'status-attended'; break;
                        }
                    }
                @endphp
                <tr class="{{ $statusClass }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $registration->name ?? __('N/A') }}</td>
                    <td>{{ $registration->email ?? __('N/A') }}</td>
                    <td>{{ $registration->phone_number ?? __('N/A') }}</td>
                    <td>{{ $registration->organization ?? __('N/A') }}</td>
                    <td>{{ $statuses[$registration->status] ?? Str::title(str_replace('_', ' ', $registration->status ?? '')) }}</td>
                    <td>{{ $registration->registered_at ? $registration->registered_at->format('d/m/Y H:i') : __('N/A') }}</td>
                    <td>{{ $registration->user ? ($registration->user->name . ' (ID: '.$registration->user_id.')') : __('Non') }}</td>
                    <td>{{ $registration->notes ?? __('N/A')}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">{{ __('Aucune inscription à afficher pour cet événement avec les filtres actuels.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        {{ $siteSettings['site_name'] ?? config('app.name') }} - {{ __('Page') }} <span class="page-number"></span>
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $text = "{PAGE_NUM} / {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 1; // Positionné à droite
            $y = $pdf->get_height() - 25;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>