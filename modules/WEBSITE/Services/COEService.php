<?php 

namespace Modules\WEBSITE\Services;

use Illuminate\Support\Str;
use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Models\CenterOfExcellence;

class COEService
{
    protected COERepositoryInterface $repository;

    public function __construct(COERepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllCOEs()
    {
        return $this->repository->all();
    }

    public function getCOEById(int $id): CenterOfExcellence
    {
        return $this->repository->findByid($id);
    }


    public function store(array $data): CenterOfExcellence
    {
        $coe = $this->repository->create($data);

        // Handle media
        if (isset($data['banner_image']) && $data['banner_image'] instanceof \Illuminate\Http\UploadedFile) {
            $coe->addMedia($data['banner_image'])->toMediaCollection('banner_image');
        }
        if (isset($data['thumb_image']) && $data['thumb_image'] instanceof \Illuminate\Http\UploadedFile) {
            $coe->addMedia($data['thumb_image'])->toMediaCollection('thumb_image');
        }
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            foreach ($data['gallery'] as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $coe->addMedia($file)->toMediaCollection('gallery');
                }
            }
        }

        return $coe;
    }

public function update(CenterOfExcellence $coe, array $data): CenterOfExcellence
{
    // Update basic fields
    $coe->update($data);

    // --- Handle media ---

    // Banner
    if (isset($data['delete_banner']) && $data['delete_banner']) {
        $coe->clearMediaCollection('banner_image');
    } elseif (isset($data['banner_image']) && $data['banner_image'] instanceof \Illuminate\Http\UploadedFile) {
        $coe->clearMediaCollection('banner_image');
        $coe->addMedia($data['banner_image'])->toMediaCollection('banner_image');
    }

    // Thumb
    if (isset($data['delete_thumb']) && $data['delete_thumb']) {
        $coe->clearMediaCollection('thumb_image');
    } elseif (isset($data['thumb_image']) && $data['thumb_image'] instanceof \Illuminate\Http\UploadedFile) {
        $coe->clearMediaCollection('thumb_image');
        $coe->addMedia($data['thumb_image'])->toMediaCollection('thumb_image');
    }

    // Gallery – delete IDs first, then add new files
    if (isset($data['deleted_gallery_ids']) && is_array($data['deleted_gallery_ids'])) {
        $coe->media()->whereIn('id', $data['deleted_gallery_ids'])->delete();
    }

    if (isset($data['gallery']) && is_array($data['gallery'])) {
        foreach ($data['gallery'] as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $coe->addMedia($file)->toMediaCollection('gallery');
            }
        }
    }

    return $coe;
}

    public function destroy(CenterOfExcellence $coe): void
    {
        $this->repository->delete($coe);
    }

    private function fillSeoDefaults(array $data): array
    {
        $data['meta_title'] = $data['meta_title'] ?: $data['name'];
        $data['meta_description'] = $data['meta_description'] ?: Str::limit(strip_tags($data['description'] ?? ''), 155);
        $data['og_title'] = $data['og_title'] ?: $data['meta_title'];
        $data['og_description'] = $data['og_description'] ?: $data['meta_description'];
        return $data;
    }
}