<x-admin-layout title="Edit Gallery Category">
    <x-slot name="header">Edit Gallery Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.gallery-categories.update', $galleryCategory) }}">
            @csrf
            @method('PUT')
            @include('admin.gallery-categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
