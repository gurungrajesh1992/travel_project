<x-admin-layout title="Difficulty Levels">
    <x-slot name="header">Difficulty Levels</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.difficulty-levels.create') }}" size="sm">Add Difficulty Level</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Difficulty Levels..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($difficultyLevels->isEmpty())
            <x-ui.empty-state title="No Difficulty Levels yet" description="Click &quot;Add Difficulty Level&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Name', '']">
                @foreach ($difficultyLevels as $difficultyLevel)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $difficultyLevel->name }}</td>


                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.difficulty-levels.edit', $difficultyLevel) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.difficulty-levels.destroy', $difficultyLevel) }}" class="inline" onsubmit="return confirm('Delete this Difficulty Level?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$difficultyLevels" />
        @endif
    </x-ui.card>
</x-admin-layout>
