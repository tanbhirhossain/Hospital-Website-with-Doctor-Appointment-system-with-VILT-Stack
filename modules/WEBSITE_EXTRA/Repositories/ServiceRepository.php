<?php
namespace Modules\WEBSITE_EXTRA\Repositories;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Interfaces\ServiceRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\Service;
use Override;
class ServiceRepository implements ServiceRepositoryInterface
{
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator {
        return Service::query()
            ->with('media')
            ->when($filters['search'] ?? null, fn($q,$s) => $q->where('title','like',"%{$s}%")->orWhere('slug','like',"%{$s}%"))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active',$filters['is_active']))
            ->orderBy('sort_order')->orderByDesc('id')
            ->paginate(15)->withQueryString();
    }
    #[Override] public function findById(int $id): Service { return Service::with('media')->findOrFail($id); }
    #[Override] public function create(array $data): Service { return Service::create($data); }
    #[Override] public function update(Service $model, array $data): Service { $model->update($data); return $model; }
    #[Override] public function delete(Service $model): bool { return (bool) $model->delete(); }
}
