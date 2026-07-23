<?php

namespace Modules\GALLERY\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\GALLERY\Interfaces\GalleryCategoryRepositoryInterface;
use Modules\GALLERY\Models\GalleryCategory;

class GalleryCategoryService
{
    public function __construct(private readonly GalleryCategoryRepositoryInterface $categories) {}

    /** @return Collection<int, GalleryCategory> */
    public function all(bool $activeOnly = false): Collection
    {
        return $this->categories->all($activeOnly);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): GalleryCategory
    {
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return DB::transaction(fn () => $this->categories->create($data));
    }

    /** @param array<string, mixed> $data */
    public function update(GalleryCategory $category, array $data): GalleryCategory
    {
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return DB::transaction(fn () => $this->categories->update($category, $data));
    }

    public function delete(GalleryCategory $category): bool
    {
        if ($category->items()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Move or delete this category’s gallery items first.',
            ]);
        }

        return DB::transaction(fn () => $this->categories->delete($category));
    }
}
