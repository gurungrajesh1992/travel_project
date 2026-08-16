<x-admin-layout title="Add FAQ">
    <x-slot name="header">Add FAQ</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.faqs.store') }}">
            @csrf
            @include('admin.faqs.form')
        </form>
    </x-ui.card>
</x-admin-layout>
