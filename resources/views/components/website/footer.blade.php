@php $company = \App\Models\CompanySetting::current(); @endphp

<footer class="bg-secondary text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div>
            <p class="text-lg font-bold">{{ $company->name }}</p>
            @if ($company->logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($company->logo) }}" alt="{{ $company->name }}" class="mt-2 h-[60px] w-[230px] object-cover">
            @endif
            <p class="mt-2 text-sm text-white/70">{{ $company->short_detail ?? 'Trekking, expeditions, and cultural tours across Nepal, India, Bhutan, and Tibet.' }}</p>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-white/50">Explore</p>
            <ul class="mt-3 space-y-2 text-sm text-white/80">
                <li><a href="{{ route('destinations.multi-country') }}" class="hover:text-white">Multi-Country Tours</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                <li><a href="{{ route('gallery.index') }}" class="hover:text-white">Gallery</a></li>
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-white/50">Support</p>
            <ul class="mt-3 space-y-2 text-sm text-white/80">
                <li><a href="{{ route('faq.index') }}" class="hover:text-white">FAQ</a></li>
                <li><a href="{{ route('pages.contact') }}" class="hover:text-white">Contact Us</a></li>
                <li><a href="{{ route('pages.about') }}" class="hover:text-white">About Us</a></li>
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-white/50">Newsletter</p>
            <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-3 flex gap-2">
                @csrf
                <input type="email" name="email" placeholder="Your email" required
                       class="min-w-0 flex-1 rounded-md border-0 px-3 py-2 text-sm text-gray-900">
                <x-ui.button size="sm">Join</x-ui.button>
            </form>
        </div>
    </div>

    <div class="border-t border-white/10 py-4 text-center text-xs text-white/50">
        &copy; {{ date('Y') }} {{ $company->name }}. All rights reserved.
    </div>
</footer>
