<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $blogPosts = BlogPost::query()
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->with('category')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog-posts.index', ['blogPosts' => $blogPosts]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.create', $this->formOptions());
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        } else {
            unset($data['featured_image']);
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post created.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', ['blogPost' => $blogPost] + $this->formOptions());
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            if ($blogPost->featured_image) {
                Storage::disk('public')->delete($blogPost->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        } else {
            unset($data['featured_image']);
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        if ($blogPost->featured_image) {
            Storage::disk('public')->delete($blogPost->featured_image);
        }

        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('status', 'Blog post deleted.');
    }

    private function formOptions(): array
    {
        return [
            'categoryOptions' => BlogCategory::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
