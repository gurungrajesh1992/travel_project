<x-admin-layout title="Edit Gallery Item">
    <x-slot name="header">Edit Gallery Item</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.gallery-items.update', $galleryItem) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.gallery-items.form')
        </form>
    </x-ui.card>
</x-admin-layout>
