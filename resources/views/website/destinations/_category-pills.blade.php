@php
    $activeCategory = $categories->firstWhere('slug', $activeCategorySlug ?? null);
@endphp

@if ($categories->isNotEmpty())
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('destinations.show', $destination) }}"
           class="px-3 py-1.5 rounded-full text-sm {{ ! ($activeCategorySlug ?? null) ? 'bg-primary text-primary-content' : 'bg-gray-100 text-gray-700 hover:bg-primary hover:text-primary-content' }}">
            All
        </a>
        @foreach ($categories as $category)
            <a href="{{ route('destinations.category', [$destination, $category['slug']]) }}"
               class="px-3 py-1.5 rounded-full text-sm {{ ($activeCategorySlug ?? null) === $category['slug'] ? 'bg-primary text-primary-content' : 'bg-gray-100 text-gray-700 hover:bg-primary hover:text-primary-content' }}">
                {{ $category['name'] }}
            </a>
        @endforeach
    </div>

    @if ($activeCategory && ! empty($activeCategory['children']))
        <div class="mt-3 flex flex-wrap gap-2 pl-4 border-l-2 border-gray-100">
            @foreach ($activeCategory['children'] as $child)
                <a href="{{ route('destinations.category', [$destination, $child['slug']]) }}"
                   class="px-3 py-1 rounded-full text-xs {{ ($activeCategorySlug ?? null) === $child['slug'] ? 'bg-primary text-primary-content' : 'bg-gray-50 text-gray-600 hover:bg-primary hover:text-primary-content' }}">
                    {{ $child['name'] }}
                </a>
            @endforeach
        </div>
    @endif
@endif
