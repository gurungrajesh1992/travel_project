<x-admin-layout title="Edit Blog Category">
    <x-slot name="header">Edit Blog Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.blog-categories.update', $blogCategory) }}">
            @csrf
            @method('PUT')
            @include('admin.blog-categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
