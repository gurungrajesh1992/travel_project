<x-customer-layout title="Profile">
    <x-slot name="header">Profile</x-slot>

    <div class="space-y-6">
        <x-ui.card>
            @include('profile.partials.update-profile-information-form')
        </x-ui.card>

        <x-ui.card>
            @include('profile.partials.update-password-form')
        </x-ui.card>

        <x-ui.card>
            @include('profile.partials.delete-user-form')
        </x-ui.card>
    </div>
</x-customer-layout>
