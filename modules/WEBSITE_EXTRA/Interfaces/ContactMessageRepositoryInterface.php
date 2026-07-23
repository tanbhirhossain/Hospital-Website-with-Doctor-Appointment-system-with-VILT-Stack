<?php

namespace Modules\WEBSITE_EXTRA\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;

interface ContactMessageRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ContactMessage;

    public function create(array $data): ContactMessage;

    public function update(ContactMessage $message, array $data): ContactMessage;

    public function delete(ContactMessage $message): bool;

    /** @return array<string, int> */
    public function countsByStatus(): array;
}
