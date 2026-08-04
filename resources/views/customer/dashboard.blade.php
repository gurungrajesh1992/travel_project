<x-customer-layout title="My Account">
    <x-slot name="header">Welcome back, {{ auth()->user()->name }}</x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-ui.stat-card label="Total Bookings" :value="$bookingCount" />
        <x-ui.stat-card label="Wishlist Items" :value="$wishlistCount" />
        <x-ui.stat-card label="Upcoming Trip" :value="$upcomingBooking?->tour?->title ?? 'None yet'" />
    </div>

    <x-ui.card title="Quick Links">
        <div class="flex flex-wrap gap-3">
            <x-ui.button as="a" href="{{ route('account.bookings.index') }}" variant="secondary">View Bookings</x-ui.button>
            <x-ui.button as="a" href="{{ route('account.payments.index') }}" variant="secondary">Payment History</x-ui.button>
            <x-ui.button as="a" href="{{ route('account.wishlist.index') }}" variant="secondary">My Wishlist</x-ui.button>
            <x-ui.button as="a" href="{{ route('account.profile.edit') }}" variant="secondary">Edit Profile</x-ui.button>
        </div>
    </x-ui.card>
</x-customer-layout>
