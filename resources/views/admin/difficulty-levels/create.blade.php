<x-admin-layout title="Add Difficulty Level">
    <x-slot name="header">Add Difficulty Level</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.difficulty-levels.store') }}">
            @csrf
            @include('admin.difficulty-levels.form')
        </form>
    </x-ui.card>
</x-admin-layout>
