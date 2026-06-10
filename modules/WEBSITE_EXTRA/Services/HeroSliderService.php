<?php
namespace Modules\WEBSITE_EXTRA\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\WEBSITE_EXTRA\Interfaces\HeroSliderRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\HeroSlider;
class HeroSliderService
{
    public function __construct(protected HeroSliderRepositoryInterface $repo) {}
    public function paginate(array $filters = []): LengthAwarePaginator { return $this->repo->all($filters); }
    public function store(array $data, int $userId): HeroSlider {
        $slider = $this->repo->create([...Arr::except($data,['slide_image']),'created_by'=>$userId,'updated_by'=>$userId]);
        if (($data['slide_image'] ?? null) instanceof UploadedFile) {
            $slider->addMedia($data['slide_image'])->toMediaCollection('slide_image');
        }
        return $slider->refresh();
    }
    public function update(int $id, array $data, int $userId): HeroSlider {
        $slider = $this->repo->findById($id);
        $slider = $this->repo->update($slider, [...Arr::except($data,['slide_image']),'updated_by'=>$userId]);
        if (($data['slide_image'] ?? null) instanceof UploadedFile) {
            $slider->clearMediaCollection('slide_image');
            $slider->addMedia($data['slide_image'])->toMediaCollection('slide_image');
        }
        return $slider->refresh();
    }
    public function destroy(int $id): bool { return $this->repo->delete($this->repo->findById($id)); }
    public function findById(int $id): HeroSlider { return $this->repo->findById($id); }
}
