<x-admin-layout title="Edit Guide">
    <x-slot name="header">Edit Guide</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.guides.update', $guide) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.guides.form')
        </form>
    </x-ui.card>
</x-admin-layout>
