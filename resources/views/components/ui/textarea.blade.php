@props(['label' => null, 'name', 'value' => null, 'rows' => 4, 'required' => false])

<div>
    @if ($label)
        <x-input-label :for="$name" :value="$label" />
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary']) }}
    >{{ old($name, $value) }}</textarea>

    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
