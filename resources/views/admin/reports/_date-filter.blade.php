@php $currentRoute = request()->route()->getName(); @endphp

<form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
    <div class="flex flex-wrap gap-1.5">
        @foreach ($presets as $key => $presetLabel)
            @continue($key === 'custom')
            <a href="{{ route($currentRoute, ['preset' => $key]) }}"
               class="px-3 py-1.5 text-xs rounded-md border {{ $preset === $key ? 'bg-primary text-primary-content border-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $presetLabel }}
            </a>
        @endforeach
    </div>

    <div class="flex items-end gap-2 sm:ml-auto">
        <input type="hidden" name="preset" value="custom">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}"
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}"
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </div>
        <x-ui.button type="submit" size="sm" variant="secondary">Apply</x-ui.button>
    </div>
</form>
