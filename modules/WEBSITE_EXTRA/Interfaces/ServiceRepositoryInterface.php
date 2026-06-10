<?php
namespace Modules\WEBSITE_EXTRA\Interfaces;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Models\Service;
interface ServiceRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): Service;
    public function create(array $data): Service;
    public function update(Service $model, array $data): Service;
    public function delete(Service $model): bool;
}
