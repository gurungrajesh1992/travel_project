<x-website-layout title="Tours">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">All Tours</h1>

        <form method="GET" class="flex flex-wrap gap-3 mb-8">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tours..."
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">

            <select name="destination" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All destinations</option>
                @foreach ($destinations as $destination)
                    <option value="{{ $destination->slug }}" @selected(request('destination') === $destination->slug)>{{ $destination->name }}</option>
                @endforeach
            </select>

            <select name="category" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>

            <button class="text-sm text-primary hover:underline">Search</button>
        </form>

        @if ($tours->isEmpty())
            <x-ui.empty-state title="No tours match your filters" description="Try clearing the search or filters." />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($tours as $tour)
                    <x-website.tour-card :tour="$tour" />
                @endforeach
            </div>
            <div class="mt-8">{{ $tours->links() }}</div>
        @endif
    </div>
</x-website-layout>
