<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $data['email']],
            ['subscribed_at' => now()]
        );

        return back()->with('status', 'Thanks for subscribing!');
    }
}
