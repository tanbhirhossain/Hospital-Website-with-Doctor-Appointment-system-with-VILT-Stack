<?php

namespace Modules\GALLERY\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Modules\GALLERY\Models\GalleryCategory;

interface GalleryCategoryRepositoryInterface
{
    /** @return Collection<int, GalleryCategory> */
    public function all(bool $activeOnly = false): Collection;

    public function find(int $id): GalleryCategory;

    /** @param array<string, mixed> $data */
    public function create(array $data): GalleryCategory;

    /** @param array<string, mixed> $data */
    public function update(GalleryCategory $category, array $data): GalleryCategory;

    public function delete(GalleryCategory $category): bool;
}
