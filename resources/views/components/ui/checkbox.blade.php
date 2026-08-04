@props(['label' => null, 'name', 'checked' => false])

<label class="inline-flex items-center gap-2">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="1"
        @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'rounded border-gray-300 text-primary shadow-sm focus:ring-primary']) }}
    />
    @if ($label)
        <span class="text-sm text-gray-700">{{ $label }}</span>
    @endif
</label>
