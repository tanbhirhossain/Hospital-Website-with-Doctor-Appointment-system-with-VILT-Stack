<?php

namespace Modules\EMAILMARKETING\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EMAILMARKETING\Models\Newsletter;

interface NewsletterRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator;

    public function allSubscribed(array $filters = []): Collection;

    public function find(int $id): Newsletter;

    public function create(array $data): Newsletter;

    public function update(Newsletter $newsletter, array $data): Newsletter;

    public function delete(Newsletter $newsletter): bool;

    public function upsertSubscriber(array $data): Newsletter;
}
