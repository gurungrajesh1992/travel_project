<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.select label="Category" name="faq_category_id" :options="$categoryOptions" :selected="$faq->faq_category_id ?? null" placeholder="None" class="sm:col-span-2" />

    <x-ui.input label="Question" name="question" :value="$faq->question ?? ''" required class="sm:col-span-2" />
    <x-ui.textarea label="Answer" name="answer" :value="$faq->answer ?? ''" :rows="5" required class="sm:col-span-2" />

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$faq->sort_order ?? 0" />

    <div>
        <input type="hidden" name="status" value="0">
        <x-ui.checkbox label="Active" name="status" :checked="$faq->status ?? true" />
    </div>
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.faqs.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
