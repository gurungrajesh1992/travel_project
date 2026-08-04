@props(['type' => 'info', 'dismissible' => false, 'autoDismiss' => null])

@php
$styles = [
    'success' => 'bg-green-50 text-green-800 border-green-200',
    'error' => 'bg-red-50 text-red-800 border-red-200',
    'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
    'info' => 'bg-blue-50 text-blue-800 border-blue-200',
];
@endphp

<div
    @if ($dismissible)
        x-data="{ show: true }"
        x-show="show"
        x-transition.duration.300ms
        @if ($autoDismiss) x-init="setTimeout(() => show = false, {{ (int) ($autoDismiss * 1000) }})" @endif
    @endif
    {{ $attributes->merge(['class' => 'rounded-md border px-4 py-3 text-sm '.($styles[$type] ?? $styles['info']).($dismissible ? ' flex items-start justify-between gap-3' : '')]) }}
>
    <div>{{ $slot }}</div>

    @if ($dismissible)
        <button type="button" @click="show = false" class="shrink-0 -m-1 p-1 rounded hover:bg-black/5 text-current/60 hover:text-current">
            <span class="sr-only">Dismiss</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
