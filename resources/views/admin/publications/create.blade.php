@extends('layouts.admin') {{-- Ou votre layout admin --}}

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
            Créer une Nouvelle Publication
        </h1>

        <form action="{{ route('admin.publications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.publications._form', [
                'publication' => null,
                'availableLocales' => $availableLocales,
                'users' => $users,
                'researchers' => $researchers,
                'publicationTypes' => $publicationTypes
            ])
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Scripts spécifiques à cette page si nécessaire, en plus de ceux de _form.blade.php --}}
@endpush