<?php

namespace Modules\WEBSITE\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\WEBSITE\Models\Department;

interface DepartmentRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function allUnpaginated(array $filters = []): Collection; // 👈 এআই-এর জন্য নতুন মেথড
    public function findById(int $id): Department;
    public function findBySlug(string $slug): Department;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;

    public function list_for_home_page(): Collection;

    public function listForNavigation(): Collection;
}
