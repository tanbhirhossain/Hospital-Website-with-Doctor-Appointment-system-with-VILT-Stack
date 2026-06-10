<?php
namespace Modules\WEBSITE_EXTRA\Repositories;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Interfaces\HeroSliderRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\HeroSlider;
use Override;
class HeroSliderRepository implements HeroSliderRepositoryInterface
{
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator {
        return HeroSlider::query()
            ->with('media')
            ->when($filters['search'] ?? null, fn($q,$s) => $q->where('title','like',"%{$s}%"))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active',$filters['is_active']))
            ->orderBy('sort_order')->orderByDesc('id')
            ->paginate(15)->withQueryString();
    }
    #[Override] public function findById(int $id): HeroSlider { return HeroSlider::with('media')->findOrFail($id); }
    #[Override] public function create(array $data): HeroSlider { return HeroSlider::create($data); }
    #[Override] public function update(HeroSlider $model, array $data): HeroSlider { $model->update($data); return $model; }
    #[Override] public function delete(HeroSlider $model): bool { return (bool) $model->delete(); }
}
