<x-admin-layout title="Add Guide">
    <x-slot name="header">Add Guide</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.guides.form')
        </form>
    </x-ui.card>
</x-admin-layout>
