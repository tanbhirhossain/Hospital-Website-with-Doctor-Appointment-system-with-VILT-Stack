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
            if (($data[$col] ?? null) instanceof UploadedFile) {
                $service->clearMediaCollection($col);
                $service->addMedia($data[$col])->toMediaCollection($col);
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
        if (! empty($data['remove_gallery']) && is_array($data['remove_gallery'])) {
            foreach ($data['remove_gallery'] as $mediaId) {
                $media = $service->media()->where('id', $mediaId)->where('collection_name', 'gallery')->first();
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Add new gallery images
        if (! empty($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $file) {
                if ($file instanceof UploadedFile) {
                    $service->addMedia($file)->toMediaCollection('gallery');
                }
            }
        }
    }
}
