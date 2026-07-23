<?php

namespace Modules\GALLERY\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GALLERY\Models\GalleryItem;
use Modules\GALLERY\Services\GalleryCategoryService;
use Modules\GALLERY\Services\GalleryItemService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryApiController extends Controller
{
    public function __construct(
        private readonly GalleryItemService $items,
        private readonly GalleryCategoryService $categories,
    ) {}

    public function index(Request $request, ?string $category = null): JsonResponse
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'category' => $category ?: $request->input('category'),
            'featured' => $request->input('featured'),
        ];

        $items = $this->items->paginate($filters, $request->integer('per_page', 12), true)
            ->through(fn (GalleryItem $item): array => $this->serializeItem($item));

        return response()->json([
            'categories' => $this->categories->all(true)->map(fn ($category): array => [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'items_count' => $category->items_count,
            ])->values(),
            'items' => $items,
        ]);
    }

    /** @return array<string, mixed> */
    private function serializeItem(GalleryItem $item): array
    {
        /** @var Media|null $media */
        $media = $item->getFirstMedia('gallery');

        return [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'description' => $item->description,
            'alt_text' => $item->alt_text ?: $item->title,
            'category' => $item->category?->only(['name', 'slug']),
            'is_featured' => $item->is_featured,
            'image_url' => $media ? ($media->hasGeneratedConversion('preview') ? $media->getUrl('preview') : $media->getUrl()) : null,
            'thumbnail_url' => $media ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl()) : null,
            'published_at' => $item->published_at?->toISOString(),
        ];
    }
}
