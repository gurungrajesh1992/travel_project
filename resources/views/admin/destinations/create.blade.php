<x-admin-layout title="Add Destination">
    <x-slot name="header">Add Destination</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.destinations.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.destinations.form')
        </form>
    </x-ui.card>
</x-admin-layout>
