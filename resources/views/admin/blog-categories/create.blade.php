<x-admin-layout title="Add Blog Category">
    <x-slot name="header">Add Blog Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.blog-categories.store') }}">
            @csrf
            @include('admin.blog-categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
