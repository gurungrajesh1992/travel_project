<x-admin-layout title="Reviews">
    <x-slot name="header">Reviews</x-slot>

    <x-ui.card>
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reviewer name..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">

            <select name="approved" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All reviews</option>
                <option value="1" @selected(request('approved') === '1')>Approved</option>
                <option value="0" @selected(request('approved') === '0')>Pending approval</option>
            </select>

            <select name="rating" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">Any rating</option>
                @foreach ([5, 4, 3, 2, 1] as $rating)
                    <option value="{{ $rating }}" @selected((string) request('rating') === (string) $rating)>{{ $rating }} star(s)</option>
                @endforeach
            </select>

            <x-ui.button type="submit" variant="secondary" size="sm">Filter</x-ui.button>
        </form>

        @if ($reviews->isEmpty())
            <x-ui.empty-state title="No reviews yet" description="Reviews submitted on tour pages will show up here for moderation." />
        @else
            <x-ui.data-table :headers="['Reviewer', 'Tour', 'Rating', 'Review', 'Submitted', 'Status', '']">
                @foreach ($reviews as $review)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $review->reviewer_name }}</p>
                            @if ($review->reviewer_country)
                                <p class="text-xs text-gray-500">{{ $review->reviewer_country }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $review->tour?->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-yellow-600">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $review->review_text }}">{{ $review->review_text }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $review->created_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$review->is_approved ? 'green' : 'yellow'">{{ $review->is_approved ? 'Approved' : 'Pending' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                            @if ($review->is_approved)
                                <form method="POST" action="{{ route('admin.reviews.unapprove', $review) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-600 hover:underline">Unapprove</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-primary hover:underline">Approve</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline" onsubmit="return confirm('Delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$reviews" />
        @endif
    </x-ui.card>
</x-admin-layout>
