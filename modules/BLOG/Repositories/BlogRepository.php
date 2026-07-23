<?php

namespace Modules\BLOG\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\BLOG\Interfaces\BlogRepositoryInterface;
use Modules\BLOG\Models\Blog;
use Override;

class BlogRepository implements BlogRepositoryInterface
{
    #[Override]
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Blog::query()
            ->with(['department:id,name,slug', 'creator:id,name', 'updater:id,name'])
            ->search($filters['search'] ?? null)
            ->when($filters['department'] ?? null, fn (Builder $query, mixed $department) => $query->where('department_id', $department))
            ->when(($filters['indexable'] ?? '') !== '', fn (Builder $query) => $query->where('is_indexable', $filters['indexable'] === '1'))
            ->when(($filters['trashed'] ?? '') === 'only', fn (Builder $query) => $query->onlyTrashed())
            ->when(($filters['trashed'] ?? '') === 'with', fn (Builder $query) => $query->withTrashed())
            ->latest('id')
            ->paginate(min(max($perPage, 1), 50))
            ->withQueryString();
    }

    #[Override]
    public function latestThree(): Collection
    {
        return Blog::with(['department', 'creator'])->latest()->take(3)->get();
    }

    #[Override]
    public function find(int $id, bool $withTrashed = false): Blog
    {
        return Blog::query()
            ->when($withTrashed, fn (Builder $query) => $query->withTrashed())
            ->with(['department', 'creator', 'updater'])
            ->findOrFail($id);
    }

    #[Override]
    public function findBySlug(string $slug): Blog
    {
        return Blog::query()
            ->with(['department:id,name,slug', 'creator:id,name'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    #[Override]
    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    #[Override]
    public function update(Blog $blog, array $data): Blog
    {
        $blog->update($data);

        return $blog->refresh()->load(['department', 'creator', 'updater']);
    }

    #[Override]
    public function delete(Blog $blog): bool
    {
        return (bool) $blog->delete();
    }

    #[Override]
    public function restore(Blog $blog): bool
    {
        return (bool) $blog->restore();
    }

    #[Override]
    public function forceDelete(Blog $blog): bool
    {
        return (bool) $blog->forceDelete();
    }

    #[Override]
    public function allActive(): Collection
    {
        return Blog::with(['department', 'creator'])->get();
    }
}
