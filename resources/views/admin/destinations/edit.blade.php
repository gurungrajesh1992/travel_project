<x-admin-layout title="Edit Destination">
    <x-slot name="header">Edit Destination</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.destinations.update', $destination) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.destinations.form')
        </form>
    </x-ui.card>
</x-admin-layout>
