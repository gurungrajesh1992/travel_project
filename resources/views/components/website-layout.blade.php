@props(['title' => null])
@php $favicon = \App\Models\CompanySetting::current()->favicon; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' - ' : '' }}{{ config('app.name') }}</title>

    @if ($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif

    <x-theme-vars panel="website" />

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    <x-website.header />

    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <x-ui.alert type="success" dismissible :auto-dismiss="5">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    <x-website.footer />
</body>
</html>
