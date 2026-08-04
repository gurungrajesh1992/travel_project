<x-website-layout title="FAQ">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Frequently Asked Questions</h1>

        <div class="space-y-8" x-data="{ open: null }">
            @forelse ($categories as $category)
                @if ($category->faqs->isNotEmpty())
                    <div id="{{ $category->slug }}">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ $category->name }}</h2>
                        <div class="space-y-2">
                            @foreach ($category->faqs as $faq)
                                <div class="border border-gray-200 rounded-md">
                                    <button @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" class="w-full text-left px-4 py-3 text-sm font-medium text-gray-900 flex justify-between items-center">
                                        {{ $faq->question }}
                                        <span x-text="open === {{ $faq->id }} ? '−' : '+'"></span>
                                    </button>
                                    <div x-show="open === {{ $faq->id }}" x-cloak class="px-4 pb-3 text-sm text-gray-600">{{ $faq->answer }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <x-ui.empty-state title="No FAQs yet" />
            @endforelse
        </div>
    </div>
</x-website-layout>
