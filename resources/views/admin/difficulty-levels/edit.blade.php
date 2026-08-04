<x-admin-layout title="Edit Difficulty Level">
    <x-slot name="header">Edit Difficulty Level</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.difficulty-levels.update', $difficultyLevel) }}">
            @csrf
            @method('PUT')
            @include('admin.difficulty-levels.form')
        </form>
    </x-ui.card>
</x-admin-layout>
