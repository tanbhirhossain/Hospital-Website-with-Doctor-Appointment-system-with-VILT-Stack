<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\QuickCard;

class QuickCardController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('site-settings.view') || $request->user()?->hasRole('super admin'), 403);

        $cards = QuickCard::orderBy('sort_order')->get();

        return Inertia::render('SITE_SETTINGS::QuickCards/Index', [
            'cards' => $cards,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'title'      => 'required|string|max:100',
            'link'       => 'nullable|string|max:500',
            'icon'       => 'nullable|string|max:50',
            'gradient'   => 'nullable|string|max:100',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? (QuickCard::max('sort_order') + 1);

        QuickCard::create($data);

        return back()->with('success', 'Quick card added successfully.');
    }

    public function update(Request $request, QuickCard $quickCard): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'title'      => 'required|string|max:100',
            'link'       => 'nullable|string|max:500',
            'icon'       => 'nullable|string|max:50',
            'gradient'   => 'nullable|string|max:100',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $quickCard->update($data);

        return back()->with('success', 'Quick card updated successfully.');
    }

    public function destroy(Request $request, QuickCard $quickCard): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $quickCard->delete();

        return back()->with('success', 'Quick card deleted successfully.');
    }
}
