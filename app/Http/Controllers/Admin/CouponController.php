<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $coupons = Coupon::query()
            ->when($request->filled('search'), fn ($q) => $q->where('code', 'like', '%'.$request->string('search').'%'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.coupons.index', ['coupons' => $coupons]);
    }

    public function create(): View
    {
        return view('admin.coupons.create', $this->formOptions());
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        $coupon = Coupon::create($data);

        $this->syncPivots($coupon, $request);

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        $coupon->load(['tours', 'categories']);

        return view('admin.coupons.edit', ['coupon' => $coupon] + $this->formOptions());
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        $coupon->update($data);

        $this->syncPivots($coupon, $request);

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon deleted.');
    }

    private function syncPivots(Coupon $coupon, Request $request): void
    {
        $coupon->tours()->sync($request->input('tours', []));
        $coupon->categories()->sync($request->input('categories', []));
    }

    private function formOptions(): array
    {
        return [
            'tours' => Tour::orderBy('title')->get(['id', 'title']),
            'categories' => Category::orderBy('parent_id')->orderBy('name')->get(['id', 'name', 'parent_id']),
        ];
    }
}
