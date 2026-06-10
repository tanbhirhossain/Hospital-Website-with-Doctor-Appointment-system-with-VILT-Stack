<?php
namespace Modules\WEBSITE_EXTRA\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\WEBSITE_EXTRA\Interfaces\HealthPackageRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\HealthPackage;
class HealthPackageService
{
    public function __construct(protected HealthPackageRepositoryInterface $repo) {}
    public function paginate(array $filters = []): LengthAwarePaginator { return $this->repo->all($filters); }
    public function store(array $data, int $userId): HealthPackage {
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
        $pkg = $this->repo->create([...Arr::except($data,['thumbnail']),'created_by'=>$userId,'updated_by'=>$userId]);
        if (($data['thumbnail'] ?? null) instanceof UploadedFile) { $pkg->addMedia($data['thumbnail'])->toMediaCollection('thumbnail'); }
        return $pkg->refresh();
    }
    public function update(int $id, array $data, int $userId): HealthPackage {
        $pkg = $this->repo->findById($id);
        $pkg = $this->repo->update($pkg,[...Arr::except($data,['thumbnail']),'updated_by'=>$userId]);
        if (($data['thumbnail'] ?? null) instanceof UploadedFile) { $pkg->clearMediaCollection('thumbnail'); $pkg->addMedia($data['thumbnail'])->toMediaCollection('thumbnail'); }
        return $pkg->refresh();
    }
    public function destroy(int $id): bool { return $this->repo->delete($this->repo->findById($id)); }
    public function findById(int $id): HealthPackage { return $this->repo->findById($id); }
}
