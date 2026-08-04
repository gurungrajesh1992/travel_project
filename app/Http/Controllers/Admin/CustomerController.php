<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::query()
            ->where('role', UserRole::Customer)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('bookings')
            ->withSum(['bookings as total_spent' => fn ($q) => $q->where('booking_status', '!=', 'cancelled')], 'total_amount')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'currency' => Tour::query()->value('currency') ?? 'USD',
        ]);
    }

    public function show(User $customer): View
    {
        abort_unless($customer->role === UserRole::Customer, 404);

        $customer->load([
            'bookings' => fn ($q) => $q->with('tour')->latest(),
            'reviews' => fn ($q) => $q->with('tour')->latest('created_at'),
            'wishlists' => fn ($q) => $q->with('tour')->latest('created_at'),
        ]);

        return view('admin.customers.show', [
            'customer' => $customer,
            'currency' => Tour::query()->value('currency') ?? 'USD',
        ]);
    }

    public function suspend(User $customer): RedirectResponse
    {
        abort_unless($customer->role === UserRole::Customer, 404);

        $customer->forceFill(['suspended_at' => now()])->save();

        return back()->with('status', $customer->name.' has been suspended and can no longer log in.');
    }

    public function activate(User $customer): RedirectResponse
    {
        abort_unless($customer->role === UserRole::Customer, 404);

        $customer->forceFill(['suspended_at' => null])->save();

        return back()->with('status', $customer->name.'\'s account has been reactivated.');
    }
}
