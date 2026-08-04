<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanySettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.company', [
            'company' => CompanySetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_detail' => ['nullable', 'string', 'max:500'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'max:1024', 'dimensions:max_width=1000,max_height=1000'],
            'favicon' => ['nullable', 'image', 'max:512', 'dimensions:max_width=512,max_height=512'],
            'banner_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $company = CompanySetting::current();

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('company', 'public');
        } else {
            unset($data['logo']);
        }

        if ($request->hasFile('favicon')) {
            if ($company->favicon) {
                Storage::disk('public')->delete($company->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('company', 'public');
        } else {
            unset($data['favicon']);
        }

        if ($request->hasFile('banner_image')) {
            if ($company->banner_image) {
                Storage::disk('public')->delete($company->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('company', 'public');
        } else {
            unset($data['banner_image']);
        }

        $company->update($data);

        return back()->with('status', 'Company information updated.');
    }
}
