<?php

namespace Modules\GALLERY\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\GALLERY\Interfaces\GalleryCategoryRepositoryInterface;
use Modules\GALLERY\Models\GalleryCategory;
use Override;

class GalleryCategoryRepository implements GalleryCategoryRepositoryInterface
{
    #[Override]
    public function all(bool $activeOnly = false): Collection
    {
        return GalleryCategory::query()
            ->when($activeOnly, fn ($query) => $query->active())
            ->withCount(['items' => function ($query) use ($activeOnly): void {
                if ($activeOnly) {
                    $query->published();
                }
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Override]
    public function find(int $id): GalleryCategory
    {
        return GalleryCategory::query()->withCount('items')->findOrFail($id);
    }

    #[Override]
    public function create(array $data): GalleryCategory
    {
        return GalleryCategory::create($data);
    }

    #[Override]
    public function update(GalleryCategory $category, array $data): GalleryCategory
    {
        $category->update($data);

        return $category->refresh()->loadCount('items');
    }

    #[Override]
    public function delete(GalleryCategory $category): bool
    {
        return (bool) $category->delete();
    }
}
