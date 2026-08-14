<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqCategoryRequest;
use App\Http\Requests\Admin\UpdateFaqCategoryRequest;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FaqCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $faqCategories = FaqCategory::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->withCount('faqs')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faq-categories.index', ['faqCategories' => $faqCategories]);
    }

    public function create(): View
    {
        return view('admin.faq-categories.create');
    }

    public function store(StoreFaqCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        FaqCategory::create($data);

        return redirect()->route('admin.faq-categories.index')->with('status', 'FAQ category created.');
    }

    public function edit(FaqCategory $faqCategory): View
    {
        return view('admin.faq-categories.edit', ['faqCategory' => $faqCategory]);
    }

    public function update(UpdateFaqCategoryRequest $request, FaqCategory $faqCategory): RedirectResponse
    {
        $faqCategory->update($request->validated());

        return redirect()->route('admin.faq-categories.index')->with('status', 'FAQ category updated.');
    }

    public function destroy(FaqCategory $faqCategory): RedirectResponse
    {
        $faqCategory->delete();

        return redirect()->route('admin.faq-categories.index')->with('status', 'FAQ category deleted.');
    }
}
