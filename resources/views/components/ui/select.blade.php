@props(['label' => null, 'name', 'options' => [], 'selected' => null, 'placeholder' => null, 'required' => false])

<div>
    @if ($label)
        <x-input-label :for="$name" :value="$label" />
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $selected) === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
