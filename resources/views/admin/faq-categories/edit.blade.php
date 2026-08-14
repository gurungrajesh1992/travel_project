<x-admin-layout title="Edit FAQ Category">
    <x-slot name="header">Edit FAQ Category</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.faq-categories.update', $faqCategory) }}">
            @csrf
            @method('PUT')
            @include('admin.faq-categories.form')
        </form>
    </x-ui.card>
</x-admin-layout>
