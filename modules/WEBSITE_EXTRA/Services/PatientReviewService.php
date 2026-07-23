<?php
namespace Modules\WEBSITE_EXTRA\Services;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\WEBSITE_EXTRA\Interfaces\PatientReviewRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\PatientReview;
class PatientReviewService
{
    public function __construct(protected PatientReviewRepositoryInterface $repo) {}
    public function paginate(array $filters = []): LengthAwarePaginator { return $this->repo->all($filters); }
    public function store(array $data, int $userId): PatientReview {
        $review = $this->repo->create([...Arr::except($data,['avatar']),'created_by'=>$userId,'updated_by'=>$userId]);
        if (($data['avatar'] ?? null) instanceof UploadedFile) { $review->addMedia($data['avatar'])->toMediaCollection('avatar'); }
        return $review->refresh();
    }
    public function update(int $id, array $data, int $userId): PatientReview {
        $review = $this->repo->findById($id);
        $review = $this->repo->update($review,[...Arr::except($data,['avatar']),'updated_by'=>$userId]);
        if (($data['avatar'] ?? null) instanceof UploadedFile) { $review->clearMediaCollection('avatar'); $review->addMedia($data['avatar'])->toMediaCollection('avatar'); }
        return $review->refresh();
    }
    public function destroy(int $id): bool { return $this->repo->delete($this->repo->findById($id)); }
    public function findById(int $id): PatientReview { return $this->repo->findById($id); }
    public function approve(int $id, int $userId): PatientReview {
        $review = $this->repo->findById($id);
        return $this->repo->update($review,['status'=>'approved','updated_by'=>$userId]);
    }
    public function reject(int $id, string $reason, int $userId): PatientReview {
        $review = $this->repo->findById($id);
        return $this->repo->update($review,['status'=>'rejected','admin_note'=>$reason,'updated_by'=>$userId]);
    }
}
