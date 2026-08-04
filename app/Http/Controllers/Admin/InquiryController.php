<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InquiryReplyMail;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = Inquiry::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('tour')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.inquiries.index', ['inquiries' => $inquiries]);
    }

    public function show(Inquiry $inquiry): View
    {
        $inquiry->load(['tour', 'respondedBy']);

        return view('admin.inquiries.show', ['inquiry' => $inquiry]);
    }

    public function reply(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'response_message' => ['required', 'string', 'max:5000'],
        ]);

        Mail::to($inquiry->email)->send(new InquiryReplyMail($inquiry, $data['response_message']));

        $inquiry->update([
            'response_message' => $data['response_message'],
            'status' => 'responded',
            'responded_by' => $request->user()->id,
            'responded_at' => now(),
        ]);

        return back()->with('status', 'Reply sent to '.$inquiry->email.'.');
    }

    public function updateStatus(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:new,responded,closed']]);

        $inquiry->update(['status' => $data['status']]);

        return back()->with('status', 'Inquiry marked as '.$data['status'].'.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('status', 'Inquiry deleted.');
    }
}
