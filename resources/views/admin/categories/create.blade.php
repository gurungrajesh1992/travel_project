<x-admin-layout title="Add Category">
    <x-slot name="header">Add Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
