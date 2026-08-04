@php
    $selectedTours = old('tours', ($coupon ?? null)?->tours->pluck('id')->all() ?? []);
    $selectedCategories = old('categories', ($coupon ?? null)?->categories->pluck('id')->all() ?? []);
@endphp

<div class="space-y-6">
    <x-ui.card title="Coupon Details" collapsible>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-ui.input label="Code" name="code" :value="$coupon->code ?? ''" required hint="Customers enter this at checkout. Stored uppercase." />
            <x-ui.select label="Discount Type" name="type" :options="['percentage' => 'Percentage (%)', 'fixed' => 'Fixed Amount']" :selected="$coupon->type ?? 'percentage'" required />

            <x-ui.input label="Value" name="value" type="number" step="0.01" min="0" :value="$coupon->value ?? ''" required hint="E.g. 10 for 10% or a fixed currency amount." />
            <x-ui.input label="Max Discount Amount (optional)" name="max_discount_amount" type="number" step="0.01" min="0" :value="$coupon->max_discount_amount ?? ''" hint="Caps the discount for percentage coupons." />

            <x-ui.input label="Minimum Booking Amount (optional)" name="min_booking_amount" type="number" step="0.01" min="0" :value="$coupon->min_booking_amount ?? ''" />
            <x-ui.input label="Usage Limit (optional)" name="usage_limit" type="number" min="1" :value="$coupon->usage_limit ?? ''" hint="Total number of times this coupon can be redeemed." />

            <x-ui.input label="Valid From (optional)" name="valid_from" type="date" :value="optional($coupon->valid_from ?? null)->format('Y-m-d')" />
            <x-ui.input label="Valid Until (optional)" name="valid_until" type="date" :value="optional($coupon->valid_until ?? null)->format('Y-m-d')" />

            @isset($coupon)
                <div class="sm:col-span-2 text-sm text-gray-500">Used {{ $coupon->used_count }} time(s){{ $coupon->usage_limit ? ' out of '.$coupon->usage_limit : '' }}.</div>
            @endisset

            <div>
                <input type="hidden" name="status" value="0">
                <x-ui.checkbox label="Active" name="status" :checked="$coupon->status ?? true" />
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Restrict To" subtitle="Leave both empty to allow this coupon on every tour." collapsible collapsed>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label value="Specific Tours" />
                <div class="mt-2 space-y-1 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @forelse ($tours as $tour)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="tours[]" value="{{ $tour->id }}"
                                   @checked(in_array($tour->id, $selectedTours))
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            {{ $tour->title }}
                        </label>
                    @empty
                        <p class="text-sm text-gray-400">No tours yet.</p>
                    @endforelse
                </div>
                <x-input-error :messages="$errors->get('tours')" class="mt-1" />
            </div>

            <div>
                <x-input-label value="Specific Categories" />
                <div class="mt-2 space-y-1 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @forelse ($categories as $category)
                        <label class="flex items-center gap-2 text-sm {{ $category->parent_id ? 'pl-4' : 'font-medium' }}">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                   @checked(in_array($category->id, $selectedCategories))
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            {{ $category->parent_id ? '— ' : '' }}{{ $category->name }}
                        </label>
                    @empty
                        <p class="text-sm text-gray-400">No categories yet.</p>
                    @endforelse
                </div>
                <x-input-error :messages="$errors->get('categories')" class="mt-1" />
            </div>
        </div>
    </x-ui.card>
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.coupons.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
