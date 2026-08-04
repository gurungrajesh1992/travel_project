@props(['label', 'value', 'hint' => null])

<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
</div>
