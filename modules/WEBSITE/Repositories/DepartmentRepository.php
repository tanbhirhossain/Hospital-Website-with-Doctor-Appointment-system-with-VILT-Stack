<?php

namespace Modules\WEBSITE\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Models\Department;
use Override;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Department::query()
            ->with(['media', 'parent:id,name'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('shortDesc', 'like', "%{$search}%");
                });
            })
            ->when($filters['parent_id'] ?? null, fn ($query, int|string $parentId): mixed => $query->where('parent_id', $parentId))
            ->when(($filters['media'] ?? null) === 'with_media', fn ($query): mixed => $query->has('media'))
            ->when(($filters['media'] ?? null) === 'without_media', fn ($query): mixed => $query->doesntHave('media'))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    #[Override]
    public function allUnpaginated(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return Department::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('slug', 'like', "%{$search}%")
                          ->orWhere('shortDesc', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->get(); // ?? ????? paginate ?? ???? get() ??????? ??? ???
    }

    #[Override]
    public function findById(int $id): Department
    {
        return Department::query()->findOrFail($id);
    }

    #[Override]
    public function findBySlug(string $slug): Department
    {
        return Department::query()->where('slug', $slug)->firstOrFail();
    }

    #[Override]
    public function create(array $data): Department
    {
        return Department::create($data);
    }

    #[Override]
    public function update(Department $department, array $data): Department
    {
        $department->update($data);

        return $department;
    }

    #[Override]
    public function delete(Department $department): bool
    {
        return (bool) $department->delete();
    }

    #[Override]
    public function list_for_home_page(): Collection
    {
        return Department::with('media')->where('is_active', 1)->whereNotNull('text-color')->get();
        
    }
}
