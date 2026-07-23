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

class GalleryController extends Controller
{
    public function __construct(
        private readonly GalleryItemService $items,
        private readonly GalleryCategoryService $categories,
    ) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('gallery.view'), 403);

        $filters = $request->only(['search', 'category', 'status', 'featured']);
        $items = $this->items->paginate($filters, 12);

        return Inertia::render('GALLERY::Index', [
            'items' => $items->through(fn (GalleryItem $item) => $this->serializeItem($item)),
            'categories' => $this->categories->all()->map(fn ($category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
                'items_count' => $category->items_count,
            ])->values(),
            'filters' => $filters,
            'can' => [
                'create' => $request->user()?->can('gallery.create') === true,
                'edit' => $request->user()?->can('gallery.edit') === true,
                'delete' => $request->user()?->can('gallery.delete') === true,
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function serializeItem(GalleryItem $item): array
    {
        /** @var Media|null $media */
        $media = $item->getFirstMedia('gallery');

        return [
            'id' => $item->id,
            'category_id' => $item->category_id,
            'category' => $item->category?->only(['id', 'name', 'slug']),
            'title' => $item->title,
            'slug' => $item->slug,
            'description' => $item->description,
            'alt_text' => $item->alt_text,
            'is_published' => $item->is_published,
            'is_featured' => $item->is_featured,
            'sort_order' => $item->sort_order,
            'published_at' => $item->published_at?->format('Y-m-d\TH:i'),
            'image_url' => $media?->getUrl(),
            'thumbnail_url' => $media
                ? ($media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl())
                : null,
            'created_at' => $item->created_at?->toFormattedDateString(),
        ];
    }
}
