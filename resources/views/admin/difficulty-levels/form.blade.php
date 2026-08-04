<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    <x-ui.input label="Name" name="name" :value="$difficultyLevel->name ?? ''" required class="sm:col-span-2" />

    <x-ui.textarea label="Description" name="description" :value="$difficultyLevel->description ?? ''" class="sm:col-span-2" />

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$difficultyLevel->sort_order ?? 0" />


</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.difficulty-levels.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
