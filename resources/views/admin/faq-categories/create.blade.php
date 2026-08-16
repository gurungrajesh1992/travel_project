<x-admin-layout title="Add FAQ Category">
    <x-slot name="header">Add FAQ Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.faq-categories.store') }}">
            @csrf
            @include('admin.faq-categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
