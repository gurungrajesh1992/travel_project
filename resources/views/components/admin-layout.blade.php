@props(['title' => null])
@php $favicon = \App\Models\CompanySetting::current()->favicon; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' - ' : '' }}{{ config('app.name') }} Admin</title>

    @if ($favicon)
        <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif

    <x-theme-vars panel="admin" />

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

    <div
        x-cloak
        x-show="sidebarOpen"
        x-on:click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/40 lg:hidden"
    ></div>

    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed z-40 inset-y-0 left-0 w-64 bg-secondary text-white transform transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
    >
        <div class="h-16 flex items-center px-6 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold text-lg truncate">
                {{ config('app.name') }}
            </a>
        </div>

        <nav class="px-3 py-4 space-y-6 overflow-y-auto" style="max-height: calc(100vh - 4rem)">
            @foreach (config('admin_nav') as $group => $items)
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-white/50">{{ $group }}</p>
                    <div class="mt-2 space-y-1">
                        @foreach ($items as $item)
                            @php
                                $hasChildren = isset($item['children']);
                                $routeExists = ! $hasChildren && $item['route'] && \Illuminate\Support\Facades\Route::has($item['route']);
                                $active = $routeExists && request()->routeIs($item['route'].'*');
                                $childActive = $hasChildren && collect($item['children'])->contains(
                                    fn ($child) => $child['route'] && \Illuminate\Support\Facades\Route::has($child['route']) && request()->routeIs($child['route'].'*')
                                );
                            @endphp

                            @if ($hasChildren)
                                <div x-data="{ open: @js($childActive) }">
                                    <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between rounded-md px-3 py-2 text-sm font-medium transition {{ $childActive ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                                        {{ $item['label'] }}
                                        <svg :class="{ 'rotate-180': open }" class="h-3.5 w-3.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak class="mt-1 ml-3 space-y-1 border-l border-white/10 pl-3">
                                        @foreach ($item['children'] as $child)
                                            @php
                                                $childRouteExists = $child['route'] && \Illuminate\Support\Facades\Route::has($child['route']);
                                                $childIsActive = $childRouteExists && request()->routeIs($child['route'].'*');
                                            @endphp
                                            @if ($childRouteExists)
                                                <a href="{{ route($child['route']) }}"
                                                   class="block rounded-md px-3 py-1.5 text-sm transition {{ $childIsActive ? 'bg-primary text-primary-content font-medium' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                                                    {{ $child['label'] }}
                                                </a>
                                            @else
                                                <span class="flex items-center justify-between rounded-md px-3 py-1.5 text-sm text-white/30 cursor-not-allowed">
                                                    {{ $child['label'] }}
                                                    <span class="text-[10px] uppercase tracking-wide border border-white/20 rounded px-1">Soon</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif ($routeExists)
                                <a href="{{ route($item['route']) }}"
                                   class="block rounded-md px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-primary text-primary-content' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                                    {{ $item['label'] }}
                                </a>
                            @else
                                <span class="flex items-center justify-between rounded-md px-3 py-2 text-sm text-white/30 cursor-not-allowed">
                                    {{ $item['label'] }}
                                    <span class="text-[10px] uppercase tracking-wide border border-white/20 rounded px-1">Soon</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <button x-on:click="sidebarOpen = true" class="lg:hidden text-gray-500">
                    <span class="sr-only">Open sidebar</span>
                    &#9776;
                </button>
                <h1 class="text-lg font-semibold text-gray-800">{{ $header ?? $title }}</h1>
            </div>

            <div class="flex items-center gap-4">
                <button class="relative text-gray-400 hover:text-gray-600" title="Notifications (module coming soon)">
                    <span class="sr-only">Notifications</span>
                    &#128276;
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                            {{ auth()->user()->name }}
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')" target="_blank">View site</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if (session('status'))
                <x-ui.alert type="success" dismissible :auto-dismiss="5" class="mb-4">{{ session('status') }}</x-ui.alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
