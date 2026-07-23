<?php

namespace Modules\GALLERY\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\GALLERY\Models\GalleryItem;
use Modules\GALLERY\Services\GalleryCategoryService;
use Modules\GALLERY\Services\GalleryItemService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicGalleryController extends Controller
{
    public function __construct(
        private readonly GalleryItemService $items,
        private readonly GalleryCategoryService $categories,
    ) {}

    public function __invoke(Request $request): Response
    {
        $category = $request->string('category')->toString();
        $items = $this->items->paginate(['category' => $category ?: null], 12, true);

        return Inertia::render('GALLERY::PublicIndex', [
            'items' => $items->through(fn (GalleryItem $item): array => $this->serialize($item)),
            'categories' => $this->categories->all(true)->map->only(['name', 'slug', 'description', 'items_count'])->values(),
            'selectedCategory' => $category,
        ]);
    }

    /** @return array<string, mixed> */
    private function serialize(GalleryItem $item): array
    {
        /** @var Media|null $media */
        $media = $item->getFirstMedia('gallery');

        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'alt_text' => $item->alt_text ?: $item->title,
            'category' => $item->category?->only(['name', 'slug']),
            'image_url' => $media ? ($media->hasGeneratedConversion('preview') ? $media->getUrl('preview') : $media->getUrl()) : null,
            'thumbnail_url' => $media ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl()) : null,
        ];
    }
}
