<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.input label="Name" name="name" :value="$guide->name ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$guide->slug ?? ''" hint="Leave blank to auto-generate from name." class="sm:col-span-2" />

    <x-ui.textarea label="Bio" name="bio" :value="$guide->bio ?? ''" class="sm:col-span-2" />

    <x-ui.input label="Languages" name="languages" :value="$guide->languages ?? ''" hint="Comma-separated, e.g. English, Nepali, Hindi" />
    <x-ui.input label="Experience (years)" name="experience_years" type="number" min="0" :value="$guide->experience_years ?? ''" />

    <x-ui.input label="Phone" name="phone" :value="$guide->phone ?? ''" />
    <x-ui.input label="Email" name="email" type="email" :value="$guide->email ?? ''" />

    <div class="sm:col-span-2">
        <x-input-label for="photo" value="Photo" />
        <input type="file" name="photo" id="photo" accept="image/*" class="mt-1 block w-full text-sm">
        @if (!empty($guide?->photo))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($guide->photo) }}" class="mt-2 h-20 w-20 object-cover rounded-full border border-gray-200">
        @endif
        <x-input-error :messages="$errors->get('photo')" class="mt-1" />
    </div>

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$guide->sort_order ?? 0" />

    <div>
        <input type="hidden" name="status" value="0">
        <x-ui.checkbox label="Active" name="status" :checked="$guide->status ?? true" />
    </div>
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.guides.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
