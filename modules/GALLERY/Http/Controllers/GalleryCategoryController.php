<?php

namespace Modules\GALLERY\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\GALLERY\Models\GalleryCategory;
use Modules\GALLERY\Requests\StoreGalleryCategoryRequest;
use Modules\GALLERY\Services\GalleryCategoryService;

class GalleryCategoryController extends Controller
{
    public function __construct(private readonly GalleryCategoryService $categories) {}

    public function store(StoreGalleryCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->validated());

        return back()->with('success', 'Gallery category created successfully.');
    }

    public function update(StoreGalleryCategoryRequest $request, GalleryCategory $galleryCategory): RedirectResponse
    {
        $this->categories->update($galleryCategory, $request->validated());

        return back()->with('success', 'Gallery category updated successfully.');
    }

    public function destroy(Request $request, GalleryCategory $galleryCategory): RedirectResponse
    {
        abort_unless($request->user()?->can('gallery.delete'), 403);
        $this->categories->delete($galleryCategory);

        return back()->with('success', 'Gallery category deleted successfully.');
    }
}
