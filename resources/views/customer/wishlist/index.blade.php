<x-customer-layout title="Wishlist">
    <x-slot name="header">My Wishlist</x-slot>

    @if ($wishlists->isEmpty())
        <x-ui.card>
            <x-ui.empty-state title="Your wishlist is empty" description="Save tours you're interested in to find them here later." />
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($wishlists as $wishlist)
                <x-ui.card>
                    <p class="font-medium text-gray-900">{{ $wishlist->tour->title }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $wishlist->tour->currency }} {{ number_format($wishlist->tour->base_price, 2) }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <a href="{{ route('tours.show', $wishlist->tour->slug) }}" class="text-sm text-primary hover:underline">View tour</a>
                        <form method="POST" action="{{ route('account.wishlist.destroy', $wishlist->tour) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Remove</button>
                        </form>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <x-ui.pagination-links :paginator="$wishlists" class="mt-4" />
    @endif
</x-customer-layout>
