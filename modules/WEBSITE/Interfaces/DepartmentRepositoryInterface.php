<?php

namespace Modules\WEBSITE\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE\Models\Department;

interface DepartmentRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): Department;
    public function findBySlug(string $slug): Department;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;
}
