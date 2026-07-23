<?php
namespace Modules\WEBSITE_EXTRA\Interfaces;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Models\ClientReview;
interface ClientReviewRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ClientReview;
    public function create(array $data): ClientReview;
    public function update(ClientReview $model, array $data): ClientReview;
    public function delete(ClientReview $model): bool;
}
