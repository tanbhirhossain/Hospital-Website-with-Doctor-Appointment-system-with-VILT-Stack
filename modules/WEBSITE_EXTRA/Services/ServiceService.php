<?php

namespace Modules\WEBSITE_EXTRA\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\WEBSITE_EXTRA\Interfaces\ServiceRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\Service;

class ServiceService
{
    public function __construct(protected ServiceRepositoryInterface $repo) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repo->all($filters);
    }

    public function store(array $data, int $userId): Service
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $service = $this->repo->create([
            ...Arr::except($data, ['thumbnail', 'banner', 'gallery', 'remove_gallery']),
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $this->syncMedia($service, $data);
        $this->syncGallery($service, $data);

        return $service->refresh();
    }

    public function update(int $id, array $data, int $userId): Service
    {
        $service = $this->repo->findById($id);

        $service = $this->repo->update($service, [
            ...Arr::except($data, ['thumbnail', 'banner', 'gallery', 'remove_gallery']),
            'updated_by' => $userId,
        ]);

        $this->syncMedia($service, $data);
        $this->syncGallery($service, $data);

        return $service->refresh();
    }

    public function destroy(int $id): bool
    {
        return $this->repo->delete($this->repo->findById($id));
    }

    public function findById(int $id): Service
    {
        return $this->repo->findById($id);
    }

    /**
     * Sync single-file media collections (thumbnail, banner).
     */
    private function syncMedia(Service $service, array $data): void
    {
        foreach (['thumbnail', 'banner'] as $col) {
            $file = $data[$col] ?? null;
            if ($file instanceof UploadedFile && $file->isValid()) {
                $service->clearMediaCollection($col);
                $service->addMedia($file)->toMediaCollection($col);
            }
        }
    }

    /**
     * Sync the gallery (multiple images) collection.
     * - Removes media IDs listed in remove_gallery
     * - Appends new files from the gallery array
     */
    private function syncGallery(Service $service, array $data): void
    {
        // Remove specific gallery images
        $removeIds = $data['remove_gallery'] ?? [];
        if (is_array($removeIds) && count($removeIds) > 0) {
            foreach ($removeIds as $mediaId) {
                $mediaId = (int) $mediaId;
                if ($mediaId > 0) {
                    $media = $service->media()
                        ->where('id', $mediaId)
                        ->where('collection_name', 'gallery')
                        ->first();
                    if ($media) {
                        $media->delete();
                    }
                }
            }
        }

        // Add new gallery images
        $galleryFiles = $data['gallery'] ?? [];
        if (is_array($galleryFiles) && count($galleryFiles) > 0) {
            foreach ($galleryFiles as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $service->addMedia($file)->toMediaCollection('gallery');
                }
            }
        }
    }
}
