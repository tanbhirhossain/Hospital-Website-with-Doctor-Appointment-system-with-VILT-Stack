<?php

namespace Modules\DOCTOR\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\DOCTOR\Interfaces\DepartmentRepositoryInterface;
use Modules\DOCTOR\Models\Department;

class DepartmentService
{
    /**
     * @var array<string, string>
     */
    private const MediaCollections = [
        'banner_image' => 'banner_image',
        'image' => 'image',
        'icon_image' => 'icon_image',
    ];

    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {}

    /**
     * @param  array{search?: string|null, parent_id?: int|string|null, media?: string|null}  $filters
     */
    public function getAllDepartments(array $filters = []): LengthAwarePaginator
    {
        return $this->departmentRepository->all($filters);
    }

    /**
     * @return Collection<int, Department>
     */
    public function getParentOptions(?int $excludeDepartmentId = null): Collection
    {
        return Department::query()
            ->select(['id', 'name'])
            ->when($excludeDepartmentId, fn ($query): mixed => $query->where('id', '!=', $excludeDepartmentId))
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{name: string, slug: string, shortDesc?: string|null, descriptions?: string|null, text_icon?: string|null, parent_id?: int|null, banner_image?: UploadedFile|null, image?: UploadedFile|null, icon_image?: UploadedFile|null}  $data
     */
    public function store(array $data, int $userId): Department
    {
        $department = $this->departmentRepository->create([
            ...Arr::except($data, array_keys(self::MediaCollections)),
            'created_by' => $userId,
        ]);

        return $this->syncMedia($department, $data);
    }

    /**
     * @param  array{name: string, slug: string, shortDesc?: string|null, descriptions?: string|null, text_icon?: string|null, parent_id?: int|null, banner_image?: UploadedFile|null, image?: UploadedFile|null, icon_image?: UploadedFile|null}  $data
     */
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

    /**
     * @param  array<string, mixed>  $data
     */
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
