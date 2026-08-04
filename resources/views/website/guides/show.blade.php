<x-website-layout :title="$guide->name">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row gap-6 items-start">
            @if ($guide->photo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($guide->photo) }}" class="h-32 w-32 rounded-full object-cover shrink-0">
            @else
                <div class="h-32 w-32 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-sm shrink-0">No photo</div>
            @endif

            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $guide->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ collect([
                        $guide->experience_years ? $guide->experience_years.' years experience' : null,
                        $guide->languages,
                    ])->filter()->implode(' · ') }}
                </p>

                @if ($guide->bio)
                    <p class="mt-4 text-gray-600 max-w-2xl">{{ $guide->bio }}</p>
                @endif

                @if ($guide->phone || $guide->email)
                    <p class="mt-4 text-sm text-gray-500">
                        {{ collect([$guide->phone, $guide->email])->filter()->implode(' · ') }}
                    </p>
                @endif
            </div>
        </div>

        @if ($guide->tours->isNotEmpty())
            <div class="mt-12">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Tours Led by {{ $guide->name }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($guide->tours as $tour)
                        <x-website.tour-card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10">
            <a href="{{ route('guides.index') }}" class="text-sm text-primary hover:underline">&larr; Back to all guides</a>
        </div>
    </div>
</x-website-layout>
