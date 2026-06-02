<?php

namespace Modules\DOCTOR\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\DOCTOR\Models\Department;

interface DepartmentRepositoryInterface
{
    /**
     * @param  array{search?: string|null, parent_id?: int|string|null, media?: string|null}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): Department;

    public function findBySlug(string $slug): Department;

    /**
     * @param  array{name: string, slug: string, shortDesc?: string|null, descriptions?: string|null, text_icon?: string|null, parent_id?: int|null, created_by?: int}  $data
     */
    public function create(array $data): Department;

    /**
     * @param  array{name: string, slug: string, shortDesc?: string|null, descriptions?: string|null, text_icon?: string|null, parent_id?: int|null}  $data
     */
    public function update(Department $department, array $data): Department;

    public function delete(Department $department): bool;
}
