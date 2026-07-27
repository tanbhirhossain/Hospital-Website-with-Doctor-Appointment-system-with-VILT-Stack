<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\NavigationMenu;

class NavigationMenuController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('navigation.manage') || $request->user()?->hasRole('super admin'), 403);

        $menus = NavigationMenu::query()
            ->where('location', 'header')
            ->orderBy('sort_order')
            ->get()
            ->load('children');

        return Inertia::render('SITE_SETTINGS::Navigation/Index', [
            'menus' => $menus,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('navigation.manage') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'label'        => 'required|string|max:100',
            'url'          => 'nullable|string|max:500',
            'route_name'   => 'nullable|string|max:100',
            'icon'         => 'nullable|string|max:50',
            'target'       => 'nullable|in:_self,_blank',
            'parent_id'    => 'nullable|exists:navigation_menus,id',
            'sort_order'   => 'nullable|integer',
            'is_active'    => 'nullable|boolean',
            'location'     => 'nullable|in:header,footer',
            'menu_type'    => 'nullable|in:link,dropdown,mega_menu',
            'config'       => 'nullable',
            'badge_text'   => 'nullable|string|max:30',
            'badge_color'  => 'nullable|string|max:50',
            'description'  => 'nullable|string|max:255',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? (NavigationMenu::max('sort_order') + 1);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['location'] = $data['location'] ?? 'header';
        $data['target'] = $data['target'] ?? '_self';

        NavigationMenu::create($data);

        return back()->with('success', 'Menu item added successfully.');
    }

    public function update(Request $request, NavigationMenu $navigationMenu): RedirectResponse
    {
        abort_unless($request->user()?->can('navigation.manage') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'label'        => 'required|string|max:100',
            'url'          => 'nullable|string|max:500',
            'route_name'   => 'nullable|string|max:100',
            'icon'         => 'nullable|string|max:50',
            'target'       => 'nullable|in:_self,_blank',
            'parent_id'    => 'nullable',
            'sort_order'   => 'nullable|integer',
            'is_active'    => 'nullable|boolean',
            'location'     => 'nullable|in:header,footer',
            'menu_type'    => 'nullable|in:link,dropdown,mega_menu',
            'config'       => 'nullable',
            'badge_text'   => 'nullable|string|max:30',
            'badge_color'  => 'nullable|string|max:50',
            'description'  => 'nullable|string|max:255',
        ]);

        // Prevent self-reference as parent
        if (isset($data['parent_id']) && $data['parent_id'] == $navigationMenu->id) {
            $data['parent_id'] = null;
        }

        $navigationMenu->update($data);

        return back()->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Request $request, NavigationMenu $navigationMenu): RedirectResponse
    {
        abort_unless($request->user()?->can('navigation.manage') || $request->user()?->hasRole('super admin'), 403);

        // Move children to parent
        NavigationMenu::where('parent_id', $navigationMenu->id)->update([
            'parent_id' => $navigationMenu->parent_id,
        ]);

        $navigationMenu->delete();

        return back()->with('success', 'Menu item deleted successfully.');
    }

    /**
     * Merge two menu items (move children from source to target, delete source).
     */
    public function merge(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('navigation.manage') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'source_id' => 'required|exists:navigation_menus,id',
            'target_id' => 'required|exists:navigation_menus,id',
        ]);

        $source = NavigationMenu::findOrFail($data['source_id']);
        $target = NavigationMenu::findOrFail($data['target_id']);

        // Move source's children to target
        NavigationMenu::where('parent_id', $source->id)->update([
            'parent_id' => $target->id,
        ]);

        // Delete source
        $source->delete();

        return back()->with('success', 'Menus merged successfully.');
    }

    /**
     * Reorder menu items.
     */
    public function reorder(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('navigation.manage') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:navigation_menus,id',
        ]);

        foreach ($data['order'] as $index => $id) {
            NavigationMenu::where('id', $id)->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Menu order updated successfully.');
    }
}
