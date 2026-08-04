@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])

<div>
    @if ($label)
        <x-input-label :for="$name" :value="$label" />
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary']) }}
    />

    @if ($hint)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
