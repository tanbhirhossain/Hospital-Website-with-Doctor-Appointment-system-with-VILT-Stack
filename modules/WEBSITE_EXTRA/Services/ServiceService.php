<?php
namespace Modules\WEBSITE_EXTRA\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\WEBSITE_EXTRA\Interfaces\ServiceRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\Service;
class ServiceService
{
    public function __construct(protected ServiceRepositoryInterface $repo) {}
    public function paginate(array $filters = []): LengthAwarePaginator { return $this->repo->all($filters); }
    public function store(array $data, int $userId): Service {
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['title']);
        $service = $this->repo->create([...Arr::except($data,['thumbnail','banner']),'created_by'=>$userId,'updated_by'=>$userId]);
        $this->syncMedia($service,$data);
        return $service->refresh();
    }
    public function update(int $id, array $data, int $userId): Service {
        $service = $this->repo->findById($id);
        $service = $this->repo->update($service,[...Arr::except($data,['thumbnail','banner']),'updated_by'=>$userId]);
        $this->syncMedia($service,$data);
        return $service->refresh();
    }
    public function destroy(int $id): bool { return $this->repo->delete($this->repo->findById($id)); }
    public function findById(int $id): Service { return $this->repo->findById($id); }
    private function syncMedia(Service $service, array $data): void {
        foreach(['thumbnail','banner'] as $col) {
            if (($data[$col] ?? null) instanceof UploadedFile) {
                $service->clearMediaCollection($col);
                $service->addMedia($data[$col])->toMediaCollection($col);
            }
        }
    }
}
