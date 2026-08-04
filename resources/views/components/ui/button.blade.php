@props(['variant' => 'primary', 'size' => 'md', 'as' => 'button', 'type' => 'submit'])

@php
$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
];

$variants = [
    'primary' => 'bg-primary text-primary-content hover:opacity-90 focus:ring-primary',
    'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-primary',
];

$classes = 'inline-flex items-center justify-center gap-2 rounded-md font-semibold shadow-sm '
    .'focus:outline-none focus:ring-2 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed '
    .($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>{{ $slot }}</button>
@endif
