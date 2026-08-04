@props(['headers' => []])

<div class="overflow-x-auto border border-gray-200 rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
