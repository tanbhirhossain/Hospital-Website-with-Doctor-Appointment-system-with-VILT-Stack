<?php
namespace Modules\WEBSITE_EXTRA\Repositories;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Interfaces\ClientReviewRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\ClientReview;
use Override;
class ClientReviewRepository implements ClientReviewRepositoryInterface
{
    #[Override]
    public function all(array $filters = []): LengthAwarePaginator {
        return ClientReview::query()
            ->with('media')
            ->when($filters['search'] ?? null, fn($q,$s) => $q->where('client_name','like',"%{$s}%")->orWhere('client_company','like',"%{$s}%"))
            ->when(isset($filters['is_active']), fn($q) => $q->where('is_active',$filters['is_active']))
            ->orderBy('sort_order')->orderByDesc('id')
            ->paginate(15)->withQueryString();
    }
    #[Override] public function findById(int $id): ClientReview { return ClientReview::with('media')->findOrFail($id); }
    #[Override] public function create(array $data): ClientReview { return ClientReview::create($data); }
    #[Override] public function update(ClientReview $model, array $data): ClientReview { $model->update($data); return $model; }
    #[Override] public function delete(ClientReview $model): bool { return (bool) $model->delete(); }
}
