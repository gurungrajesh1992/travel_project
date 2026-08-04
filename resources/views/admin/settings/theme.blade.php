<x-admin-layout title="Theme Settings">
    <x-slot name="header">Settings &mdash; Theme</x-slot>

    <form method="POST" action="{{ route('admin.settings.theme.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach ($panels as $panel => $colors)
            <x-ui.card :title="ucfirst($panel).' panel'" :subtitle="'Colors used across the '.$panel.' experience'" collapsible :collapsed="!$loop->first">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($colors as $key => $value)
                        @continue(str_ends_with($key, '_content'))
                        <div>
                            <x-input-label :for="$panel.'_'.$key" :value="ucfirst(str_replace('_', ' ', $key))" />
                            <div class="mt-1 flex items-center gap-2">
                                <input type="color"
                                       id="{{ $panel }}_{{ $key }}"
                                       name="{{ $panel }}[{{ $key }}]"
                                       value="{{ $value }}"
                                       class="h-9 w-14 rounded border border-gray-300">
                                <input type="text"
                                       value="{{ $value }}"
                                       oninput="document.getElementById('{{ $panel }}_{{ $key }}').value = this.value"
                                       class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endforeach

        <x-ui.button type="submit">Save Theme</x-ui.button>
    </form>
</x-admin-layout>
