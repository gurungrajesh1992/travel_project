<x-admin-layout title="Edit Page">
    <x-slot name="header">Edit Page</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.pages.form')
        </form>
    </x-ui.card>
</x-admin-layout>
