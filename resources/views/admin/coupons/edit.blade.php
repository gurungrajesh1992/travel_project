<x-admin-layout title="Edit Coupon">
    <x-slot name="header">Edit Coupon</x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
            @csrf
            @method('PUT')
            @include('admin.coupons.form')
        </form>
    </x-ui.card>
</x-admin-layout>
