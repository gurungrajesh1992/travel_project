<x-admin-layout title="Edit FAQ">
    <x-slot name="header">Edit FAQ</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
            @csrf
            @method('PUT')
            @include('admin.faqs.form')
        </form>
    </x-ui.card>
</x-admin-layout>
