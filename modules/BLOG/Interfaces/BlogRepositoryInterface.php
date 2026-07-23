<?php

namespace Modules\BLOG\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\BLOG\Models\Blog;

interface BlogRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function find(int $id, bool $withTrashed = false): Blog;

    public function findBySlug(string $slug): Blog;

    public function create(array $data): Blog;

    public function update(Blog $blog, array $data): Blog;

    public function delete(Blog $blog): bool;

    public function restore(Blog $blog): bool;

    public function forceDelete(Blog $blog): bool;

    public function latestThree(): Collection;

    public function allActive(): Collection;
}
