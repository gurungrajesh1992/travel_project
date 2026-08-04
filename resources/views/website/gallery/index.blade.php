<x-website-layout title="Gallery">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
        <h1 class="text-2xl font-bold text-gray-900">Gallery</h1>

        @forelse ($categories as $category)
            @if ($category->items->isNotEmpty())
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $category->name }}</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($category->items as $item)
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                @if ($item->file_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item->file_path) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400 text-xs">{{ $item->caption ?? 'Image' }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <x-ui.empty-state title="Gallery coming soon" />
        @endforelse
    </div>
</x-website-layout>
