@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">{{ $event->title }}</h1>
    <p class="text-gray-600 mb-6">{{ $event->start_datetime->format('d/m/Y H:i') }} - {{ $event->end_datetime->format('d/m/Y H:i') }}</p>
    <div class="prose mb-6">
        {!! $event->description !!}
    </div>
    @if($event->registration_url)
        <a href="{{ $event->registration_url }}" class="px-4 py-2 bg-blue-600 text-white rounded">S'inscrire</a>
    @endif
    @if($registrations->isNotEmpty())
        <h2 class="text-2xl font-semibold mt-8">Inscriptions confirmées</h2>
        <ul class="list-disc pl-6 mt-4">
            @foreach($registrations as $registration)
                <li>{{ $registration->name }} ({{ $registration->email }})</li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
