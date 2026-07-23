<?php
namespace Modules\WEBSITE_EXTRA\Repositories;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Interfaces\HealthPackageRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\HealthPackage;
use Override;
class HealthPackageRepository implements HealthPackageRepositoryInterface
{
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator {
        return HealthPackage::query()
            ->with('media')
            ->when($filters['search'] ?? null, fn($q,$s) => $q->where('name','like',"%{$s}%")->orWhere('slug','like',"%{$s}%"))
            ->when($filters['category'] ?? null, fn($q,$c) => $q->where('category',$c))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active',$filters['is_active']))
            ->orderBy('sort_order')->orderByDesc('id')
            ->paginate(15)->withQueryString();
    }
    #[Override] public function findById(int $id): HealthPackage { return HealthPackage::with('media')->findOrFail($id); }
    #[Override] public function create(array $data): HealthPackage { return HealthPackage::create($data); }
    #[Override] public function update(HealthPackage $model, array $data): HealthPackage { $model->update($data); return $model; }
    #[Override] public function delete(HealthPackage $model): bool { return (bool) $model->delete(); }
}
