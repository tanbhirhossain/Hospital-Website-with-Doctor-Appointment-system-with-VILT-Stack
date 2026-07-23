<?php

namespace Modules\GALLERY\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\GALLERY\Models\GalleryItem;
use Modules\GALLERY\Requests\StoreGalleryItemRequest;
use Modules\GALLERY\Services\GalleryItemService;

class GalleryItemController extends Controller
{
    public function __construct(private readonly GalleryItemService $items) {}

    public function store(StoreGalleryItemRequest $request): RedirectResponse
    {
        $this->items->create($request->safe()->except(['image']), $request->file('image'));

        return to_route('gallery.index')->with('success', 'Gallery image added successfully.');
    }

    public function update(StoreGalleryItemRequest $request, GalleryItem $galleryItem): RedirectResponse
    {
        $this->items->update($galleryItem, $request->safe()->except(['image']), $request->file('image'));

        return back()->with('success', 'Gallery image updated successfully.');
    }

    public function destroy(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        abort_unless($request->user()?->can('gallery.delete'), 403);
        $this->items->delete($galleryItem);

        return back()->with('success', 'Gallery image deleted successfully.');
    }
}
