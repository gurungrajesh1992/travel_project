<x-admin-layout title="Add Gallery Item">
    <x-slot name="header">Add Gallery Item</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.gallery-items.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.gallery-items.form')
        </form>
    </x-ui.card>
</x-admin-layout>
