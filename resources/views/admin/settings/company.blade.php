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

            </div>
        </x-ui.card>

        <x-ui.card title="Contact Information" collapsible collapsed>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-ui.input label="Address" name="address" :value="$company->address ?? ''" class="sm:col-span-2" />
                <x-ui.input label="Email" name="email" type="email" :value="$company->email ?? ''" />
                <x-ui.input label="Contact Number" name="contact_number" :value="$company->contact_number ?? ''" />
                <x-ui.input label="WhatsApp Number" name="whatsapp_number" :value="$company->whatsapp_number ?? ''"
                            hint="Include country code, digits only (e.g. 9779841234567). Powers the floating WhatsApp chat button." />
                <x-ui.input label="KakaoTalk Channel/Chat URL" name="kakao_url" :value="$company->kakao_url ?? ''"
                             hint="Full link to your KakaoTalk channel or open chat (e.g. https://pf.kakao.com/_yourchannel/chat). Powers the floating KakaoTalk button." class="sm:col-span-2" />
            </div>
        </x-ui.card>

        <x-ui.button type="submit">Save Company Info</x-ui.button>
    </form>

    <x-ui.card title="Homepage Banner Slider" subtitle="Shown as the rotating hero banner on the website homepage. Add one or more images, or a YouTube video link." collapsible class="mt-6">
        @if ($banners->isNotEmpty())
            <div class="flex flex-wrap gap-4 mb-6">
                @foreach ($banners as $banner)
                    <div class="relative">
                        @if ($banner->media_type === 'image' && $banner->file_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->file_path) }}" class="h-24 w-40 object-cover rounded border border-gray-200">
                        @else
                            <div class="h-24 w-40 flex flex-col items-center justify-center bg-gray-100 rounded border border-gray-200 text-xs text-gray-500 p-2 text-center">
                                <span>YouTube Video</span>
                                @if ($banner->title)
                                    <span class="mt-1 truncate w-full">{{ $banner->title }}</span>
                                @endif
                            </div>
                        @endif
                        <form method="POST" action="{{ route('admin.settings.company.banners.destroy', $banner) }}"
                              class="absolute -top-2 -right-2" onsubmit="return confirm('Remove this banner?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center h-6 w-6 rounded-full bg-red-600 text-white text-xs leading-none hover:bg-red-700">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 mb-6">No banners added yet. The homepage will show a default static banner until you add one.</p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <form method="POST" action="{{ route('admin.settings.company.banners.store') }}" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <x-input-label for="files" value="Add Banner Image(s)" />
                <input type="file" name="files[]" id="files" accept="image/*" multiple class="block w-full text-sm">
                <p class="text-xs text-gray-500">Recommended wide aspect ratio (e.g. 1920&times;800px), max 4MB each.</p>
                <x-input-error :messages="$errors->get('files.*')" class="mt-1" />
                <x-ui.button type="submit" variant="secondary">Add Image(s)</x-ui.button>
            </form>

            <form method="POST" action="{{ route('admin.settings.company.banners.store') }}" class="space-y-2">
                @csrf
                <x-ui.input label="Add Banner Video (YouTube URL)" name="video_url" placeholder="https://www.youtube.com/watch?v=..." />
                <x-ui.input label="Title (optional)" name="title" />
                <x-ui.button type="submit" variant="secondary">Add Video</x-ui.button>
            </form>
        </div>
    </x-ui.card>
</x-admin-layout>
