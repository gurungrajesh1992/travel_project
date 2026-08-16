<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNewsletterRequest;
use App\Mail\NewsletterMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('search'), fn ($q) => $q->where('email', 'like', '%'.$request->string('search').'%'))
            ->latest('subscribed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.newsletter.index', ['subscribers' => $subscribers]);
    }

    public function create(): View
    {
        return view('admin.newsletter.create', [
            'subscriberCount' => NewsletterSubscriber::count(),
        ]);
    }

    public function send(SendNewsletterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $subscribers = NewsletterSubscriber::pluck('email');

        foreach ($subscribers as $email) {
            Mail::to($email)->queue(new NewsletterMail($data['subject'], $data['body']));
        }

        return redirect()->route('admin.newsletter.index')->with('status', "Newsletter queued for {$subscribers->count()} subscriber(s).");
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return redirect()->route('admin.newsletter.index')->with('status', 'Subscriber removed.');
    }
}
