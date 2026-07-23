<?php

namespace Modules\EMAILMARKETING\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\EMAILMARKETING\Interfaces\NewsletterRepositoryInterface;
use Modules\EMAILMARKETING\Models\Newsletter;

class NewsletterRepository implements NewsletterRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        return Newsletter::query()
            ->search($filters['search'] ?? null)
            ->when(($filters['status'] ?? '') !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(($filters['audience_type'] ?? '') !== '', fn (Builder $query) => $query->where('audience_type', $filters['audience_type']))
            ->when(($filters['source'] ?? '') !== '', fn (Builder $query) => $query->where('source', $filters['source']))
            ->when(($filters['country'] ?? '') !== '', fn (Builder $query) => $query->where('country', $filters['country']))
            ->latest('id')
            ->paginate(min(max($perPage, 1), 100), ['*'], $pageName)
            ->withQueryString();
    }

    public function allSubscribed(array $filters = []): Collection
    {
        return Newsletter::query()
            ->subscribed()
            ->when(($filters['audience_type'] ?? '') !== '', fn (Builder $query) => $query->where('audience_type', $filters['audience_type']))
            ->when(($filters['source'] ?? '') !== '', fn (Builder $query) => $query->where('source', $filters['source']))
            ->when(($filters['country'] ?? '') !== '', fn (Builder $query) => $query->where('country', $filters['country']))
            ->latest('id')
            ->get();
    }

    public function find(int $id): Newsletter
    {
        return Newsletter::findOrFail($id);
    }

    public function create(array $data): Newsletter
    {
        return Newsletter::create($data);
    }

    public function update(Newsletter $newsletter, array $data): Newsletter
    {
        $newsletter->update($data);

        return $newsletter->refresh();
    }

    public function delete(Newsletter $newsletter): bool
    {
        return (bool) $newsletter->delete();
    }

    public function upsertSubscriber(array $data): Newsletter
    {
        $email = mb_strtolower(trim((string) $data['email']));
        $newsletter = Newsletter::query()->firstOrNew(['email' => $email]);
        $newsletter->fill(array_merge($data, [
            'email' => $email,
            'isActive' => true,
            'status' => 'subscribed',
            'unsubscribed_at' => null,
        ]));
        $newsletter->subscribed_at ??= now();
        $newsletter->unsubscribe_token ??= Newsletter::makeToken();
        $newsletter->save();

        return $newsletter->refresh();
    }
}
