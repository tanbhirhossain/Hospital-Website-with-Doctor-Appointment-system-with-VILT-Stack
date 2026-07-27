<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\WhyChooseUsItem;

class WhyChooseUsController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('site-settings.view') || $request->user()?->hasRole('super admin'), 403);

        $items = WhyChooseUsItem::orderBy('sort_order')->get();

        return Inertia::render('SITE_SETTINGS::WhyChooseUs/Index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'gradient'    => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:50',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? (WhyChooseUsItem::max('sort_order') + 1);

        WhyChooseUsItem::create($data);

        return back()->with('success', 'Feature added successfully.');
    }

    public function update(Request $request, WhyChooseUsItem $whyChooseUsItem): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:50',
            'gradient'    => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:50',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $whyChooseUsItem->update($data);

        return back()->with('success', 'Feature updated successfully.');
    }

    public function destroy(Request $request, WhyChooseUsItem $whyChooseUsItem): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $whyChooseUsItem->delete();

        return back()->with('success', 'Feature deleted successfully.');
    }
}
