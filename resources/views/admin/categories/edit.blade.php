<x-admin-layout title="Edit Category">
    <x-slot name="header">Edit Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
