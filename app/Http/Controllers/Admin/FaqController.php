<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $faqs = Faq::query()
            ->when($request->filled('search'), fn ($q) => $q->where('question', 'like', '%'.$request->string('search').'%'))
            ->with('category')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faqs.index', ['faqs' => $faqs]);
    }

    public function create(): View
    {
        return view('admin.faqs.create', $this->formOptions());
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ created.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', ['faq' => $faq] + $this->formOptions());
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ deleted.');
    }

    private function formOptions(): array
    {
        return [
            'categoryOptions' => FaqCategory::orderBy('sort_order')->pluck('name', 'id'),
        ];
    }
}
