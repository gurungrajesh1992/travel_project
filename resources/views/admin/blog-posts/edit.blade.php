<x-admin-layout title="Edit Blog Post">
    <x-slot name="header">Edit Blog Post</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.blog-posts.update', $blogPost) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.blog-posts.form')
        </form>
    </x-ui.card>
</x-admin-layout>
