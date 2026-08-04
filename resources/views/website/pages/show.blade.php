<x-website-layout :title="$page->title">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>

        <div class="prose max-w-none">
            {!! $page->content !!}
        </div>

        @if ($page->slug === 'contact-us')
            <x-ui.card title="Send us a message" class="mt-10">
                <form method="POST" action="{{ route('pages.contact.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-ui.input label="Name" name="name" required />
                        <x-ui.input label="Email" name="email" type="email" required />
                    </div>
                    <x-ui.input label="Subject" name="subject" />
                    <x-ui.textarea label="Message" name="message" required />
                    <x-ui.button type="submit">Send Message</x-ui.button>
                </form>
            </x-ui.card>
        @endif
    </div>
</x-website-layout>
