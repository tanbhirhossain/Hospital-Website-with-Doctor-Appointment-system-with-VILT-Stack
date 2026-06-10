<?php
namespace Modules\WEBSITE_EXTRA\Interfaces;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Models\PatientReview;
interface PatientReviewRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): PatientReview;
    public function create(array $data): PatientReview;
    public function update(PatientReview $model, array $data): PatientReview;
    public function delete(PatientReview $model): bool;
}
