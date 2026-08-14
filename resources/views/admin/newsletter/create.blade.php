<x-admin-layout title="Compose Newsletter">
    <x-slot name="header">Compose &amp; Send Newsletter</x-slot>

    <x-ui.card>
        <p class="text-sm text-gray-500 mb-4">This will be emailed to all {{ $subscriberCount }} subscriber(s).</p>

        <form method="POST" action="{{ route('admin.newsletter.send') }}">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <x-ui.input label="Subject" name="subject" :value="old('subject')" required />
                <x-ui.textarea label="Message" name="body" :value="old('body')" :rows="10" required />
            </div>

            <div class="mt-6 flex gap-3">
                <x-ui.button type="submit" onclick="return confirm('Send this newsletter to {{ $subscriberCount }} subscriber(s)?')">Send Newsletter</x-ui.button>
                <x-ui.button as="a" href="{{ route('admin.newsletter.index') }}" variant="secondary">Cancel</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-admin-layout>
