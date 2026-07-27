<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\HomeVideo;

class HomeVideoController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('site-settings.view') || $request->user()?->hasRole('super admin'), 403);

        $videos = HomeVideo::orderBy('sort_order')->get();

        return Inertia::render('SITE_SETTINGS::HomeVideos/Index', [
            'videos' => $videos,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'title'        => 'nullable|string|max:255',
            'video_url'    => 'required|string|max:500',
            'video_type'   => 'nullable|in:youtube,vimeo,file',
            'description'  => 'nullable|string',
            'thumbnail'    => 'nullable|image|max:2048',
            'is_active'    => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;
        $data['video_type'] = $data['video_type'] ?? 'youtube';
        $data['sort_order'] = $data['sort_order'] ?? (HomeVideo::max('sort_order') + 1);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('home-videos/thumbnails', 'public');
        }

        HomeVideo::create($data);

        return back()->with('success', 'Video added successfully.');
    }

    public function update(Request $request, HomeVideo $homeVideo): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'title'        => 'nullable|string|max:255',
            'video_url'    => 'required|string|max:500',
            'video_type'   => 'nullable|in:youtube,vimeo,file',
            'description'  => 'nullable|string',
            'thumbnail'    => 'nullable|image|max:2048',
            'is_active'    => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($homeVideo->thumbnail_path && !str_starts_with($homeVideo->thumbnail_path, 'http')) {
                Storage::disk('public')->delete($homeVideo->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('home-videos/thumbnails', 'public');
        }

        $homeVideo->update($data);

        return back()->with('success', 'Video updated successfully.');
    }

    public function destroy(Request $request, HomeVideo $homeVideo): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        if ($homeVideo->thumbnail_path && !str_starts_with($homeVideo->thumbnail_path, 'http')) {
            Storage::disk('public')->delete($homeVideo->thumbnail_path);
        }

        $homeVideo->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
