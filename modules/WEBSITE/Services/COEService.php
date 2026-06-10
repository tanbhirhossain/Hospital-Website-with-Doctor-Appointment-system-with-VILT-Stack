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
        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data = $this->fillSeoDefaults($data);

        return $this->repository->create($data);
    }

    public function update(CenterOfExcellence $coe, array $data): CenterOfExcellence
    {
        $data['slug'] = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data = $this->fillSeoDefaults($data);

        return $this->repository->update($coe, $data);
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