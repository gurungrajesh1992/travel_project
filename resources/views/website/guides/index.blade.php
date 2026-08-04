<x-website-layout title="Our Guides">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold text-gray-900">Our Guides</h1>
        <p class="mt-2 text-gray-600 max-w-2xl">Meet the local experts who lead our treks and tours.</p>

        @if ($guides->isEmpty())
            <p class="mt-10 text-gray-500">No guides listed yet — check back soon.</p>
        @else
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($guides as $guide)
                    <a href="{{ route('guides.show', $guide) }}" class="block bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition text-center p-5">
                        @if ($guide->photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($guide->photo) }}" class="h-24 w-24 mx-auto rounded-full object-cover">
                        @else
                            <div class="h-24 w-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No photo</div>
                        @endif
                        <p class="mt-3 font-semibold text-gray-900">{{ $guide->name }}</p>
                        @if ($guide->experience_years)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $guide->experience_years }} years experience</p>
                        @endif
                        @if ($guide->languages)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $guide->languages }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-website-layout>
