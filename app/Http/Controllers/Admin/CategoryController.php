<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->orderBy('sort_order')

            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'parentOptions' => \App\Models\Category::whereNull('parent_id')->orderBy('name')->pluck('name', 'id'),

        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: \Illuminate\Support\Str::slug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parentOptions' => \App\Models\Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->pluck('name', 'id'),

        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted.');
    }
}
