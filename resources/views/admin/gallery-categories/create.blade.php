<x-admin-layout title="Add Gallery Category">
    <x-slot name="header">Add Gallery Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.gallery-categories.store') }}">
            @csrf
            @include('admin.gallery-categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
