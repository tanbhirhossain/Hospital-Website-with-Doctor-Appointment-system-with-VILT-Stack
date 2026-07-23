<?php
namespace Modules\WEBSITE_EXTRA\Interfaces;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Models\HeroSlider;
interface HeroSliderRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): HeroSlider;
    public function create(array $data): HeroSlider;
    public function update(HeroSlider $model, array $data): HeroSlider;
    public function delete(HeroSlider $model): bool;

    public function allforHomepage();
}
