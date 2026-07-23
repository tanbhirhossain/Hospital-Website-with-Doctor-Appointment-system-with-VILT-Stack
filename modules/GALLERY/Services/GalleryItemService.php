<?php

namespace Modules\GALLERY\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\GALLERY\Interfaces\GalleryItemRepositoryInterface;
use Modules\GALLERY\Models\GalleryItem;

class GalleryItemService
{
    public function __construct(private readonly GalleryItemRepositoryInterface $items) {}

    /** @param array<string, mixed> $filters */
    public function paginate(array $filters = [], int $perPage = 12, bool $publishedOnly = false): LengthAwarePaginator
    {
        return $this->items->paginate($filters, $perPage, $publishedOnly);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, UploadedFile $image): GalleryItem
    {
        return DB::transaction(function () use ($data, $image): GalleryItem {
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']).'-'.Str::lower(Str::random(6));
            $item = $this->items->create($data);
            $item->addMedia($image)->usingName($data['title'])->toMediaCollection('gallery');

            return $item->load(['category', 'media']);
        });
    }

    /** @param array<string, mixed> $data */
    public function update(GalleryItem $item, array $data, ?UploadedFile $image = null): GalleryItem
    {
        return DB::transaction(function () use ($item, $data, $image): GalleryItem {
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']).'-'.$item->id;
            $item = $this->items->update($item, $data);

            if ($image) {
                $item->addMedia($image)->usingName($data['title'])->toMediaCollection('gallery');
            }

            return $item->refresh()->load(['category', 'media']);
        });
    }

    public function delete(GalleryItem $item): bool
    {
        return DB::transaction(fn () => $this->items->delete($item));
    }
}
