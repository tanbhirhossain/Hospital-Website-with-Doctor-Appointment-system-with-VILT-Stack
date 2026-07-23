<?php

namespace Modules\GALLERY\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\GALLERY\Models\GalleryItem;

interface GalleryItemRepositoryInterface
{
    /** @param array<string, mixed> $filters */
    public function paginate(array $filters = [], int $perPage = 12, bool $publishedOnly = false): LengthAwarePaginator;

    public function allforHome(): Collection;

    public function find(int $id): GalleryItem;

    /** @param array<string, mixed> $data */
    public function create(array $data): GalleryItem;

    /** @param array<string, mixed> $data */
    public function update(GalleryItem $item, array $data): GalleryItem;

    public function delete(GalleryItem $item): bool;
}
