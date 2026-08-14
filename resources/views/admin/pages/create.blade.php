<x-admin-layout title="Add Page">
    <x-slot name="header">Add Page</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.pages.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.pages.form')
        </form>
    </x-ui.card>
</x-admin-layout>
