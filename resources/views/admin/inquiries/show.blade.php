@php
    $statusColors = ['new' => 'yellow', 'responded' => 'green', 'closed' => 'gray'];
@endphp

<x-admin-layout title="Inquiry from {{ $inquiry->name }}">
    <x-slot name="header">Inquiry from {{ $inquiry->name }}</x-slot>

    <div class="space-y-6">
        <x-ui.card title="Message" collapsible>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 text-sm mb-4">
                <dt class="text-gray-500">From</dt>
                <dd class="text-gray-900">{{ $inquiry->name }} &lt;{{ $inquiry->email }}&gt;</dd>

                @if ($inquiry->phone)
                    <dt class="text-gray-500">Phone</dt>
                    <dd class="text-gray-900">{{ $inquiry->phone }}</dd>
                @endif

                <dt class="text-gray-500">Tour</dt>
                <dd class="text-gray-900">{{ $inquiry->tour?->title ?? 'General inquiry (Contact Us)' }}</dd>

                <dt class="text-gray-500">Subject</dt>
                <dd class="text-gray-900">{{ $inquiry->subject ?? '—' }}</dd>

                <dt class="text-gray-500">Received</dt>
                <dd class="text-gray-900">{{ $inquiry->created_at?->format('M j, Y g:i A') }}</dd>

                <dt class="text-gray-500">Status</dt>
                <dd><x-ui.badge :color="$statusColors[$inquiry->status] ?? 'gray'">{{ ucfirst($inquiry->status) }}</x-ui.badge></dd>
            </dl>

            <div class="border-t border-gray-100 pt-4">
                <p class="text-sm font-medium text-gray-700 mb-1">Message</p>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $inquiry->message }}</p>
            </div>
        </x-ui.card>

        @if ($inquiry->response_message)
            <x-ui.card title="Your Reply" subtitle="Sent {{ $inquiry->responded_at?->format('M j, Y g:i A') }} by {{ $inquiry->respondedBy?->name ?? 'System' }}" collapsible collapsed>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $inquiry->response_message }}</p>
            </x-ui.card>
        @endif

        <x-ui.card title="Reply by Email" subtitle="Sends an email to {{ $inquiry->email }} and marks this inquiry as responded." collapsible>
            <form method="POST" action="{{ route('admin.inquiries.reply', $inquiry) }}" class="space-y-3">
                @csrf
                <x-ui.textarea label="Reply" name="response_message" rows="6" :value="$inquiry->response_message ?? ''" required />
                <x-ui.button type="submit">Send Reply</x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card title="Status" collapsible collapsed>
            <form method="POST" action="{{ route('admin.inquiries.status', $inquiry) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                @method('PATCH')
                <x-ui.select label="Change Status" name="status"
                             :options="['new' => 'New', 'responded' => 'Responded', 'closed' => 'Closed']"
                             :selected="$inquiry->status" />
                <x-ui.button type="submit" size="sm">Update</x-ui.button>
            </form>

            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" class="mt-4" onsubmit="return confirm('Delete this inquiry?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:underline">Delete Inquiry</button>
            </form>
        </x-ui.card>

        <div>
            <x-ui.button as="a" href="{{ route('admin.inquiries.index') }}" variant="secondary">Back to Inquiries</x-ui.button>
        </div>
    </div>
</x-admin-layout>
