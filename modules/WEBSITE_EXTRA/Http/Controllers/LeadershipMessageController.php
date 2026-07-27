<?php

namespace Modules\WEBSITE_EXTRA\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Models\LeadershipMessage;

class LeadershipMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $messages = LeadershipMessage::orderBy('sort_order')->get();

        return Inertia::render('WEBSITE_EXTRA::LeadershipMessage/Index', [
            'messages' => $messages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'role'         => 'nullable|string|max:100',
            'role_line'    => 'nullable|string|max:200',
            'eyebrow'      => 'nullable|string|max:100',
            'title'        => 'nullable|string|max:255',
            'quote'        => 'nullable|string|max:500',
            'credentials'  => 'nullable|string',
            'photo'        => 'nullable|image|max:2048',
            'message'      => 'nullable|string',
            'is_active'    => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);

        // Parse credentials (newline separated)
        if (isset($data['credentials'])) {
            $data['credentials'] = json_encode(
                array_filter(array_map('trim', explode("\n", $data['credentials'])))
            );
        }

        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? (LeadershipMessage::max('sort_order') + 1);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('leadership-messages', 'public');
        }

        LeadershipMessage::create($data);

        return back()->with('success', 'Leadership message added successfully.');
    }

    public function update(Request $request, LeadershipMessage $leadershipMessage): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'role'         => 'nullable|string|max:100',
            'role_line'    => 'nullable|string|max:200',
            'eyebrow'      => 'nullable|string|max:100',
            'title'        => 'nullable|string|max:255',
            'quote'        => 'nullable|string|max:500',
            'credentials'  => 'nullable|string',
            'photo'        => 'nullable|image|max:2048',
            'message'      => 'nullable|string',
            'is_active'    => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);

        if (isset($data['credentials'])) {
            $data['credentials'] = json_encode(
                array_filter(array_map('trim', explode("\n", $data['credentials'])))
            );
        }

        if ($request->hasFile('photo')) {
            if ($leadershipMessage->photo_path && !str_starts_with($leadershipMessage->photo_path, 'http')) {
                Storage::disk('public')->delete($leadershipMessage->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('leadership-messages', 'public');
        }

        $leadershipMessage->update($data);

        return back()->with('success', 'Leadership message updated successfully.');
    }

    public function destroy(Request $request, LeadershipMessage $leadershipMessage): RedirectResponse
    {
        if ($leadershipMessage->photo_path && !str_starts_with($leadershipMessage->photo_path, 'http')) {
            Storage::disk('public')->delete($leadershipMessage->photo_path);
        }

        $leadershipMessage->delete();

        return back()->with('success', 'Leadership message deleted successfully.');
    }
}
