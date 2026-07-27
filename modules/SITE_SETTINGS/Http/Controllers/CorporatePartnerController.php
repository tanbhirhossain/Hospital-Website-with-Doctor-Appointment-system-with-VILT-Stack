<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\CorporatePartner;

class CorporatePartnerController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('site-settings.view') || $request->user()?->hasRole('super admin'), 403);

        $partners = CorporatePartner::orderBy('sort_order')->get();

        return Inertia::render('SITE_SETTINGS::CorporatePartners/Index', [
            'partners' => $partners,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'website_url' => 'nullable|url|max:255',
            'logo'        => 'nullable|image|max:1024',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? (CorporatePartner::max('sort_order') + 1);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('corporate-partners', 'public');
        }

        CorporatePartner::create($data);

        return back()->with('success', 'Corporate partner added successfully.');
    }

    public function update(Request $request, CorporatePartner $corporatePartner): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'website_url' => 'nullable|url|max:255',
            'logo'        => 'nullable|image|max:1024',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        if ($request->hasFile('logo')) {
            if ($corporatePartner->logo_path && !str_starts_with($corporatePartner->logo_path, 'http')) {
                Storage::disk('public')->delete($corporatePartner->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('corporate-partners', 'public');
        }

        $corporatePartner->update($data);

        return back()->with('success', 'Corporate partner updated successfully.');
    }

    public function destroy(Request $request, CorporatePartner $corporatePartner): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        if ($corporatePartner->logo_path && !str_starts_with($corporatePartner->logo_path, 'http')) {
            Storage::disk('public')->delete($corporatePartner->logo_path);
        }

        $corporatePartner->delete();

        return back()->with('success', 'Corporate partner deleted successfully.');
    }
}
