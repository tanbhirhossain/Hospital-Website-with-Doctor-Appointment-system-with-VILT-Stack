<?php

namespace Modules\WEBSITE_EXTRA\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\WEBSITE_EXTRA\Interfaces\ContactMessageRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;
use Override;

class ContactMessageRepository implements ContactMessageRepositoryInterface
{
    #[Override]
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ContactMessage::query()
            ->search($filters['search'] ?? null)
            ->when(($filters['status'] ?? '') !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(($filters['mail_status'] ?? '') !== '', fn (Builder $query) => $query->where('mail_status', $filters['mail_status']))
            ->when(($filters['department'] ?? '') !== '', fn (Builder $query) => $query->where('department', $filters['department']))
            ->latest('id')
            ->paginate(min(max($perPage, 1), 100))
            ->withQueryString();
    }

    #[Override]
    public function findById(int $id): ContactMessage
    {
        return ContactMessage::findOrFail($id);
    }

    #[Override]
    public function create(array $data): ContactMessage
    {
        return ContactMessage::create($data);
    }

    #[Override]
    public function update(ContactMessage $message, array $data): ContactMessage
    {
        $message->update($data);

        return $message->refresh();
    }

    #[Override]
    public function delete(ContactMessage $message): bool
    {
        return (bool) $message->delete();
    }

    #[Override]
    public function countsByStatus(): array
    {
        $counts = ContactMessage::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return [
            ContactMessage::STATUS_NEW => (int) ($counts[ContactMessage::STATUS_NEW] ?? 0),
            ContactMessage::STATUS_READ => (int) ($counts[ContactMessage::STATUS_READ] ?? 0),
            ContactMessage::STATUS_RESOLVED => (int) ($counts[ContactMessage::STATUS_RESOLVED] ?? 0),
            ContactMessage::STATUS_ARCHIVED => (int) ($counts[ContactMessage::STATUS_ARCHIVED] ?? 0),
        ];
    }
}
