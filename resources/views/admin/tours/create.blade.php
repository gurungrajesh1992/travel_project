<x-admin-layout title="Add Tour">
    <x-slot name="header">Add Tour</x-slot>

    @vite(['resources/js/map-picker.js'])

    <form method="POST" action="{{ route('admin.tours.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.tours.form', ['tour' => null])
    </form>
</x-admin-layout>
