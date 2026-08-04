<x-admin-layout title="Company Settings">
    <x-slot name="header">Settings &mdash; Company Info</x-slot>

    <form method="POST" action="{{ route('admin.settings.company.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <x-ui.card title="Branding" subtitle="Shown in the website navigation and footer." collapsible>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-ui.input label="Company Name" name="name" :value="$company->name" required class="sm:col-span-2" />
                <x-ui.textarea label="Short Detail" name="short_detail" :value="$company->short_detail ?? ''"
                               hint="A one or two sentence description shown under the logo in the footer." class="sm:col-span-2" />

                <div>
                    <x-input-label for="logo" value="Logo" />
                    <input type="file" name="logo" id="logo" accept="image/*" class="mt-1 block w-full text-sm">
                    <p class="mt-1 text-xs text-gray-500">Square image recommended, max 1000&times;1000px and 1MB. Falls back to the company name text if empty.</p>
                    @if ($company->logo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($company->logo) }}" class="mt-2 h-12 w-auto rounded border border-gray-200 p-1">
                    @endif
                    <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="favicon" value="Favicon" />
                    <input type="file" name="favicon" id="favicon" accept="image/*" class="mt-1 block w-full text-sm">
                    <p class="mt-1 text-xs text-gray-500">Browser tab icon. Square image recommended, max 512&times;512px. Falls back to the default icon if empty.</p>
                    @if ($company->favicon)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($company->favicon) }}" class="mt-2 h-8 w-8 rounded border border-gray-200 p-1">
                    @endif
                    <x-input-error :messages="$errors->get('favicon')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="banner_image" value="Banner Image" />
                    <input type="file" name="banner_image" id="banner_image" accept="image/*" class="mt-1 block w-full text-sm">
                    <p class="mt-1 text-xs text-gray-500">Reserved for a future website homepage banner. Max 4MB.</p>
                    @if ($company->banner_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($company->banner_image) }}" class="mt-2 h-16 w-full max-w-xs object-cover rounded border border-gray-200">
                    @endif
                    <x-input-error :messages="$errors->get('banner_image')" class="mt-1" />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Contact Information" collapsible collapsed>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-ui.input label="Address" name="address" :value="$company->address ?? ''" class="sm:col-span-2" />
                <x-ui.input label="Email" name="email" type="email" :value="$company->email ?? ''" />
                <x-ui.input label="Contact Number" name="contact_number" :value="$company->contact_number ?? ''" />
            </div>
        </x-ui.card>

        <x-ui.button type="submit">Save Company Info</x-ui.button>
    </form>
</x-admin-layout>
