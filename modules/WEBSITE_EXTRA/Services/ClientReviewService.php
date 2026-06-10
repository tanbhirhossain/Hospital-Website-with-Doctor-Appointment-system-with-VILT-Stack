<?php
namespace Modules\WEBSITE_EXTRA\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\WEBSITE_EXTRA\Interfaces\ClientReviewRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\ClientReview;
class ClientReviewService
{
    public function __construct(protected ClientReviewRepositoryInterface $repo) {}
    public function paginate(array $filters = []): LengthAwarePaginator { return $this->repo->all($filters); }
    public function store(array $data, int $userId): ClientReview {
        $review = $this->repo->create([...Arr::except($data,['avatar']),'created_by'=>$userId,'updated_by'=>$userId]);
        if (($data['avatar'] ?? null) instanceof UploadedFile) { $review->addMedia($data['avatar'])->toMediaCollection('avatar'); }
        return $review->refresh();
    }
    public function update(int $id, array $data, int $userId): ClientReview {
        $review = $this->repo->findById($id);
        $review = $this->repo->update($review,[...Arr::except($data,['avatar']),'updated_by'=>$userId]);
        if (($data['avatar'] ?? null) instanceof UploadedFile) { $review->clearMediaCollection('avatar'); $review->addMedia($data['avatar'])->toMediaCollection('avatar'); }
        return $review->refresh();
    }
    public function destroy(int $id): bool { return $this->repo->delete($this->repo->findById($id)); }
    public function findById(int $id): ClientReview { return $this->repo->findById($id); }
}
