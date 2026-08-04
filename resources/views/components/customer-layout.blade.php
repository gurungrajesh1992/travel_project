@props(['title' => null])
@php $favicon = \App\Models\CompanySetting::current()->favicon; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' - ' : '' }}{{ config('app.name') }} Account</title>

    @if ($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif

    <x-theme-vars panel="customer" />

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <x-website.header />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1">
            <nav class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-x-auto lg:overflow-hidden">
                <div class="flex lg:block whitespace-nowrap lg:whitespace-normal">
                    @foreach ([
                        ['label' => 'Overview', 'route' => 'account.dashboard'],
                        ['label' => 'Profile', 'route' => 'account.profile.edit'],
                        ['label' => 'Bookings', 'route' => 'account.bookings.index'],
                        ['label' => 'Payments', 'route' => 'account.payments.index'],
                        ['label' => 'Inquiries', 'route' => 'account.inquiries.index'],
                        ['label' => 'Wishlist', 'route' => 'account.wishlist.index'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="shrink-0 lg:shrink block px-4 py-3 text-sm font-medium border-b-4 lg:border-b-0 lg:border-l-4 {{ request()->routeIs($item['route']) ? 'border-primary text-primary bg-primary/5' : 'border-transparent text-gray-600 hover:bg-gray-50' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </nav>
        </aside>

        <div class="lg:col-span-3">
            @if (session('status'))
                <x-ui.alert type="success" dismissible :auto-dismiss="5" class="mb-6">{{ session('status') }}</x-ui.alert>
            @endif

            @isset($header)
                <h1 class="text-xl font-semibold text-gray-900 mb-6">{{ $header }}</h1>
            @endisset

            {{ $slot }}
        </div>
    </div>

    <x-website.footer />
</body>
</html>
