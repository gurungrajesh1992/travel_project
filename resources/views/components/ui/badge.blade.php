@props(['color' => 'gray'])

@php
$colors = [
    'gray' => 'bg-gray-100 text-gray-700',
    'green' => 'bg-green-100 text-green-700',
    'red' => 'bg-red-100 text-red-700',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'blue' => 'bg-blue-100 text-blue-700',
    'primary' => 'bg-primary/10 text-primary',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium '.($colors[$color] ?? $colors['gray'])]) }}>
    {{ $slot }}
</span>
