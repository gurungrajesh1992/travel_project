<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::published()->with('category')->latest('published_at')->paginate(9);

        return view('website.blog.index', compact('posts'));
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->status === 'published', 404);

        return view('website.blog.show', compact('post'));
    }
}
