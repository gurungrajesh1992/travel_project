@props(['tour'])

<a href="{{ route('tours.show', $tour) }}" class="block bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition">
    <div class="h-44 bg-gray-100">
        @if ($tour->thumbnail)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($tour->thumbnail) }}" class="h-full w-full object-cover">
        @else
            <div class="h-full w-full flex items-center justify-center text-gray-400 text-sm">No image</div>
        @endif
    </div>
    <div class="p-4">
        <p class="text-xs font-medium text-primary uppercase tracking-wide">
            {{ $tour->destinationsLabel() }} @if ($tour->categoriesLabel()) &middot; {{ $tour->categoriesLabel() }} @endif
        </p>
        <p class="mt-1 font-semibold text-gray-900">{{ $tour->title }}</p>
        <p class="mt-1 text-sm text-gray-500">{{ $tour->duration_days }} days &middot; {{ $tour->difficulty?->name ?? 'Any level' }}</p>
        <p class="mt-2 font-semibold text-gray-900">{{ $tour->currency }} {{ number_format($tour->base_price, 0) }} <span class="text-xs font-normal text-gray-500">/ person</span></p>
    </div>
</a>
