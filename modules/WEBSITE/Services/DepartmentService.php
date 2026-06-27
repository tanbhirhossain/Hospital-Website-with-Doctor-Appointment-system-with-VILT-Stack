<?php

namespace Modules\WEBSITE\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Models\Department;

class DepartmentService
{

    private const MediaCollections = [
        'banner_image' => 'banner_image',
        'image' => 'image',
        'icon_image' => 'icon_image',
    ];

    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {}


    
    public function getAllDepartments(array $filters = []): LengthAwarePaginator
    {
        return $this->departmentRepository->all($filters);
    }

    public function getAllDepartmentsForAI(array $filters = []): Collection
    {
        // 👈 এটি সরাসরি সব ডেটা Collection আকারে রিটার্ন করবে
        return $this->departmentRepository->allUnpaginated($filters); 
    }

    public function getParentOptions(?int $excludeDepartmentId = null): Collection
    {
        return Department::query()
            ->select(['id', 'name'])
            ->when($excludeDepartmentId, fn ($query): mixed => $query->where('id', '!=', $excludeDepartmentId))
            ->orderBy('name')
            ->get();
    }


    public function store(array $data, int $userId): Department
    {
        $department = $this->departmentRepository->create([
            ...Arr::except($data, array_keys(self::MediaCollections)),
            'created_by' => $userId,
        ]);

        return $this->syncMedia($department, $data);
    }


    public function update(int $id, array $data): Department
    {
        $department = $this->departmentRepository->findById($id);
        $department = $this->departmentRepository->update($department, Arr::except($data, array_keys(self::MediaCollections)));
        return $this->syncMedia($department, $data);
    }

    public function destroy(int $id): bool
    {
        $department = $this->departmentRepository->findById($id);
        return $this->departmentRepository->delete($department);
    }


    private function syncMedia(Department $department, array $data): Department
    {
        foreach (self::MediaCollections as $field => $collection) {
            if (($data[$field] ?? null) instanceof UploadedFile) {
                $department->addMedia($data[$field])->toMediaCollection($collection);
            }
        }

        return $department->refresh();
    }
}
