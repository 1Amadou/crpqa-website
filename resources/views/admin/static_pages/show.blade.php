@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de la Page :') }} <span class="italic">{{ $staticPage->title }}</span>
        </h2>
        <div>
            <a href="{{ route('admin.static-pages.edit', $staticPage) }}" class="px-4 py-2 bg-sky-500 text-white rounded-md hover:bg-sky-600 text-sm font-medium mr-2">
                {{ __('Modifier cette Page') }}
            </a>
            <a href="{{ route('admin.static-pages.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Titre') }}</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $staticPage->title }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Slug (URL)') }}</dt>
                    <dd class="mt-1 text-md text-gray-700 font-mono bg-gray-100 px-2 py-1 rounded inline-block">{{ $staticPage->slug }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Statut') }}</dt>
                    <dd class="mt-1 text-md text-gray-900">
                        @if($staticPage->is_published)
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Publiée
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Brouillon (Non Publiée)
                            </span>
                        @endif
                    </dd>
                </div>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500 mb-1">{{ __('Contenu de la Page') }}</dt>
                {{-- Attention : {!! !!} est utilisé pour afficher du HTML. Assurez-vous que le contenu est fiable. --}}
                {{-- Si vous n'utilisez pas d'éditeur HTML pour le contenu, remplacez par {{ $staticPage->content }} --}}
                <div class="mt-1 p-4 border rounded-md bg-gray-50 prose prose-sm max-w-none">
                    {!! $staticPage->content !!}
                </div>
            </div>

            @if($staticPage->meta_title || $staticPage->meta_description)
                <div class="border-t pt-4 mt-4">
                    <h4 class="text-md font-semibold text-gray-700 mb-2">Informations SEO</h4>
                    @if($staticPage->meta_title)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Méta Titre') }}</dt>
                            <dd class="mt-1 text-md text-gray-900">{{ $staticPage->meta_title }}</dd>
                        </div>
                    @endif
                    @if($staticPage->meta_description)
                        <div class="mt-2">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Méta Description') }}</dt>
                            <dd class="mt-1 text-md text-gray-600">{{ $staticPage->meta_description }}</dd>
                        </div>
                    @endif
                </div>
            @endif

            <div class="border-t pt-4 mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Créée par') }}</dt>
                    <dd class="mt-1 text-md text-gray-900">{{ $staticPage->user->name ?? 'Utilisateur inconnu' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Créée le') }}</dt>
                    <dd class="mt-1 text-md text-gray-900">{{ $staticPage->created_at->format('d/m/Y à H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Dernière mise à jour le') }}</dt>
                    <dd class="mt-1 text-md text-gray-900">{{ $staticPage->updated_at->format('d/m/Y à H:i') }}</dd>
                </div>
            </div>
        </div>
    </div>
@endsection
