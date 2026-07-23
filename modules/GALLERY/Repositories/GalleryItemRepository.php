<?php

namespace Modules\GALLERY\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\GALLERY\Interfaces\GalleryItemRepositoryInterface;
use Modules\GALLERY\Models\GalleryItem;
use Override;

class GalleryItemRepository implements GalleryItemRepositoryInterface
{
    #[Override]
    public function paginate(array $filters = [], int $perPage = 12, bool $publishedOnly = false): LengthAwarePaginator
    {
        return GalleryItem::query()
            ->with(['category:id,name,slug', 'media'])
            ->when($publishedOnly, fn (Builder $query) => $query
                ->published()
                ->whereHas('category', fn (Builder $category) => $category->active()))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, function (Builder $query, mixed $category): void {
                $query->whereHas('category', fn (Builder $builder) => $builder
                    ->where(is_numeric($category) ? 'id' : 'slug', $category));
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $query) => $query
                ->where('is_published', $filters['status'] === 'published'))
            ->when(($filters['featured'] ?? null) === '1', fn (Builder $query) => $query->where('is_featured', true))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->latest('id')
            ->paginate(min(max($perPage, 1), 48))
            ->withQueryString();
    }

    #[Override]
        public function allforHome(): Collection
        {
            return GalleryItem::with('category')
                ->published()
                ->orderBy('sort_order')
                ->latest('published_at')
                ->get();
        }

    #[Override]
    public function find(int $id): GalleryItem
    {
        return GalleryItem::query()->with(['category', 'media'])->findOrFail($id);
    }

    #[Override]
    public function create(array $data): GalleryItem
    {
        return GalleryItem::create($data);
    }

    #[Override]
    public function update(GalleryItem $item, array $data): GalleryItem
    {
        $item->update($data);

        return $item->refresh()->load(['category', 'media']);
    }

    #[Override]
    public function delete(GalleryItem $item): bool
    {
        return (bool) $item->delete();
    }
}
