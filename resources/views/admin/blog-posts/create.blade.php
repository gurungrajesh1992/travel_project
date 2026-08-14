<x-admin-layout title="Add Blog Post">
    <x-slot name="header">Add Blog Post</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.blog-posts.form')
        </form>
    </x-ui.card>
</x-admin-layout>
