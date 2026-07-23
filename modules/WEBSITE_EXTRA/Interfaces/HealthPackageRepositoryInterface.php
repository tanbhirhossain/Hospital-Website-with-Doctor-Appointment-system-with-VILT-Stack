<?php
namespace Modules\WEBSITE_EXTRA\Interfaces;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\WEBSITE_EXTRA\Models\HealthPackage;
interface HealthPackageRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function findById(int $id): HealthPackage;
    public function create(array $data): HealthPackage;
    public function update(HealthPackage $model, array $data): HealthPackage;
    public function delete(HealthPackage $model): bool;
}
