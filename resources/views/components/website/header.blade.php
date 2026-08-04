@php
    $company = \App\Models\CompanySetting::current();

    $nav = \Illuminate\Support\Facades\Cache::remember('nav.destinations', 3600, function () {
        $navDestinations = \App\Models\Destination::query()
            ->topLevel()
            ->active()
            ->orderBy('sort_order')
            ->with(['tours' => fn ($q) => $q->published()->with('categories:id')])
            ->get()
            ->map(function ($destination) {
                $usedCategoryIds = $destination->tours->flatMap(fn ($tour) => $tour->categories->pluck('id'))->unique();

                return [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'slug' => $destination->slug,
                    'categories' => \App\Services\CategoryNavResolver::resolve($usedCategoryIds)->all(),
                ];
            })
            ->all();

        $multiCountryCombos = \App\Services\MultiCountryNavResolver::combos()->all();

        return ['destinations' => $navDestinations, 'multiCountry' => $multiCountryCombos];
    });

    $navDestinations = $nav['destinations'];
    $multiCountryCombos = $nav['multiCountry'];
@endphp

<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center sm:justify-between gap-3 sm:gap-4">
        <a href="/" class="flex items-center justify-center w-full sm:w-auto">
            @if ($company->logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($company->logo) }}" alt="{{ $company->name }}" class="h-[60px] w-[230px] object-cover">
            @else
                <span class="text-xl font-bold text-primary">{{ $company->name }}</span>
            @endif
        </a>

        @if ($company->contact_number)
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->contact_number) }}" class="flex items-center gap-3 group shrink-0">
                <span class="flex items-center justify-center h-10 w-10 rounded-full bg-primary/10 text-primary shrink-0 p-[10px]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-xs text-gray-500">24/7 Support</span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-primary">{{ $company->contact_number }}</span>
                </span>
            </a>
        @endif
    </div>
</div>

<header class="sticky top-0 z-50 bg-white border-b border-gray-200" x-data="{ mobileOpen: false }"
        x-effect="document.body.classList.toggle('overflow-hidden', mobileOpen)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            <nav class="hidden lg:flex items-center gap-1" x-data="{ open: null }">
                <a href="/" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">Home</a>

                @foreach ($navDestinations as $destination)
                    <div class="relative" @mouseenter="open = {{ $destination['id'] }}" @mouseleave="open = null">
                        <a href="/destinations/{{ $destination['slug'] }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">
                            {{ $destination['name'] }}
                        </a>
                        @if (! empty($destination['categories']))
                            <div x-show="open === {{ $destination['id'] }}" x-cloak
                                 class="absolute left-0 top-full w-60 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-40">
                                @foreach ($destination['categories'] as $category)
                                    <div class="relative group">
                                        <a href="/destinations/{{ $destination['slug'] }}/{{ $category['slug'] }}"
                                           class="flex items-center justify-between px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary">
                                            {{ $category['name'] }}
                                            @if (! empty($category['children']))
                                                <svg class="h-3 w-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                </svg>
                                            @endif
                                        </a>

                                        @if (! empty($category['children']))
                                            <div class="hidden group-hover:block absolute left-full top-0 w-56 bg-white border border-gray-200 rounded-md shadow-lg py-1">
                                                @foreach ($category['children'] as $child)
                                                    <a href="/destinations/{{ $destination['slug'] }}/{{ $child['slug'] }}"
                                                       class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary">
                                                        {{ $child['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="relative" @mouseenter="open = 'multi-country'" @mouseleave="open = null">
                    <a href="/multi-country" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">Multi-Country</a>
                    @if (! empty($multiCountryCombos))
                        <div x-show="open === 'multi-country'" x-cloak
                             class="absolute left-0 top-full w-56 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-40">
                            @foreach ($multiCountryCombos as $combo)
                                <a href="/multi-country?destinations={{ $combo['slugs'] }}"
                                   class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary">
                                    {{ $combo['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <span class="px-3 py-2 text-sm font-medium text-gray-400 cursor-not-allowed" title="Coming in phase 2">Hotel</span>
                <span class="px-3 py-2 text-sm font-medium text-gray-400 cursor-not-allowed" title="Coming in phase 2">Vehicle</span>
                <a href="{{ route('guides.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">Guides</a>
                <a href="/about-us" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">About</a>
                <a href="/contact-us" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">Contact</a>
                <a href="/blog" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">Blog</a>
                <a href="/gallery" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">Gallery</a>
                <a href="/faq" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">FAQ</a>
            </nav>

            <div class="flex items-center gap-2 ml-auto">
                @auth
                    @php
                        $accountUrl = auth()->user()->hasAnyRole(['admin', 'staff']) ? route('admin.dashboard') : route('account.dashboard');
                    @endphp
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="flex items-center justify-center h-9 w-9 rounded-full bg-gray-100 text-gray-600 hover:text-primary hover:bg-primary/10">
                            <span class="sr-only">Account</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak
                             class="absolute right-0 top-full w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-40">
                            <p class="px-4 py-2 text-sm font-medium text-gray-900 border-b border-gray-100 truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <a href="{{ $accountUrl }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary">
                                My Account
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-primary">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-primary">Login</a>
                @endauth

                <button @click="mobileOpen = ! mobileOpen" class="lg:hidden flex items-center justify-center h-9 w-9 rounded-md text-gray-600 hover:text-primary hover:bg-gray-100">
                    <span class="sr-only">Toggle menu</span>
                    <svg x-show="! mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <nav x-show="mobileOpen" x-cloak x-data="{ openDestination: null, openCategory: null, openMultiCountry: false }"
         class="lg:hidden absolute inset-x-0 top-full h-screen z-40 bg-white overflow-y-auto px-4 pb-6 pt-3 border-t border-gray-100">
        <a href="/" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">Home</a>

            @foreach ($navDestinations as $destination)
                <div>
                    <button @click="openDestination = openDestination === {{ $destination['id'] }} ? null : {{ $destination['id'] }}"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">
                        <a href="/destinations/{{ $destination['slug'] }}" @click.stop="mobileOpen = false">{{ $destination['name'] }}</a>
                        @if (! empty($destination['categories']))
                            <span x-text="openDestination === {{ $destination['id'] }} ? '−' : '+'"></span>
                        @endif
                    </button>
                    @if (! empty($destination['categories']))
                        <div x-show="openDestination === {{ $destination['id'] }}" x-cloak class="pl-6 space-y-1">
                            @foreach ($destination['categories'] as $category)
                                <div>
                                    <button @click="openCategory = openCategory === {{ $category['id'] }} ? null : {{ $category['id'] }}"
                                            class="w-full flex items-center justify-between px-3 py-1.5 text-sm text-gray-600 hover:text-primary">
                                        <a href="/destinations/{{ $destination['slug'] }}/{{ $category['slug'] }}" @click.stop="mobileOpen = false">{{ $category['name'] }}</a>
                                        @if (! empty($category['children']))
                                            <span x-text="openCategory === {{ $category['id'] }} ? '−' : '+'"></span>
                                        @endif
                                    </button>
                                    @if (! empty($category['children']))
                                        <div x-show="openCategory === {{ $category['id'] }}" x-cloak class="pl-4 space-y-1">
                                            @foreach ($category['children'] as $child)
                                                <a href="/destinations/{{ $destination['slug'] }}/{{ $child['slug'] }}"
                                                   class="block px-3 py-1 text-sm text-gray-500 hover:text-primary" @click="mobileOpen = false">
                                                    {{ $child['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <div>
                <button @click="openMultiCountry = ! openMultiCountry"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary">
                    <a href="/multi-country" @click.stop="mobileOpen = false">Multi-Country</a>
                    @if (! empty($multiCountryCombos))
                        <span x-text="openMultiCountry ? '−' : '+'"></span>
                    @endif
                </button>
                @if (! empty($multiCountryCombos))
                    <div x-show="openMultiCountry" x-cloak class="pl-6 space-y-1">
                        @foreach ($multiCountryCombos as $combo)
                            <a href="/multi-country?destinations={{ $combo['slugs'] }}"
                               class="block px-3 py-1.5 text-sm text-gray-600 hover:text-primary" @click="mobileOpen = false">
                                {{ $combo['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <span class="block px-3 py-2 text-sm font-medium text-gray-400">Hotel <span class="text-xs">(coming soon)</span></span>
            <span class="block px-3 py-2 text-sm font-medium text-gray-400">Vehicle <span class="text-xs">(coming soon)</span></span>
            <a href="{{ route('guides.index') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">Guides</a>
            <a href="/about-us" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">About</a>
            <a href="/contact-us" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">Contact</a>
            <a href="/blog" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">Blog</a>
            <a href="/gallery" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">Gallery</a>
            <a href="/faq" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary" @click="mobileOpen = false">FAQ</a>
    </nav>
</header>
