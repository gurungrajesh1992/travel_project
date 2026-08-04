<x-admin-layout title="Add Coupon">
    <x-slot name="header">Add Coupon</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            @include('admin.coupons.form')
        </form>
    </x-ui.card>
</x-admin-layout>
