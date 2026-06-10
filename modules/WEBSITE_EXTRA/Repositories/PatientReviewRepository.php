<?php
namespace Modules\WEBSITE_EXTRA\Repositories;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Interfaces\PatientReviewRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\PatientReview;
use Override;
class PatientReviewRepository implements PatientReviewRepositoryInterface
{
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator {
        return PatientReview::query()
            ->with(['doctor:id,name','media'])
            ->when($filters['search'] ?? null, fn($q,$s) => $q->where('patient_name','like',"%{$s}%")->orWhere('patient_phone','like',"%{$s}%"))
            ->when($filters['status'] ?? null, fn($q,$s) => $q->where('status',$s))
            ->when($filters['doctor_id'] ?? null, fn($q,$d) => $q->where('doctor_id',$d))
            ->orderByDesc('id')
            ->paginate(15)->withQueryString();
    }
    #[Override] public function findById(int $id): PatientReview { return PatientReview::with(['doctor','media'])->findOrFail($id); }
    #[Override] public function create(array $data): PatientReview { return PatientReview::create($data); }
    #[Override] public function update(PatientReview $model, array $data): PatientReview { $model->update($data); return $model; }
    #[Override] public function delete(PatientReview $model): bool { return (bool) $model->delete(); }
}
