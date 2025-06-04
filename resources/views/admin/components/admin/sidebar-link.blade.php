{{-- resources/views/components/admin/sidebar-link.blade.php --}}
@props([
    'href' => '#',
    'active' => false,
    'icon' => '',
    'isSubItem' => false
])

@php
    $baseClasses = 'flex items-center space-x-3 py-2.5 px-3 rounded-md transition-all duration-200 ease-in-out text-sm font-medium group ';
    $subItemIndentClass = $isSubItem ? 'pl-10 ' : ' ';
    $activeClasses = 'bg-primary-600 dark:bg-primary-500 text-white shadow-sm';
    $inactiveClasses = 'text-slate-300 dark:text-gray-400 hover:bg-slate-700 dark:hover:bg-gray-700 hover:text-white dark:hover:text-gray-100';
    
    $iconColorClass = $active ? 'text-white' : 'text-slate-400 dark:text-gray-400 group-hover:text-slate-200 dark:group-hover:text-gray-100';
    if ($isSubItem && $active) {
        $iconColorClass = 'text-primary-200 dark:text-primary-300';
    }
    $iconSizeClass = $isSubItem ? 'text-lg' : 'text-xl';
@endphp

<a href="{{ $href }}" class="{{ $baseClasses }} {{ $subItemIndentClass }} {{ $active ? $activeClasses : $inactiveClasses }}">
    @if($icon)
        <ion-icon name="{{ $icon }}" class="{{ $iconSizeClass }} {{ $iconColorClass }} flex-shrink-0"></ion-icon>
    @elseif($isSubItem)
        <span class="w-5 h-5 mr-3 flex-shrink-0"></span> {{-- Espace pour alignement --}}
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>