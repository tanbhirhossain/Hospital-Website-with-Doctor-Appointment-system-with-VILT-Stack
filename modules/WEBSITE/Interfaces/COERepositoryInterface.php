<?php 

namespace Modules\WEBSITE\Interfaces;

use Modules\WEBSITE\Models\CenterOfExcellence;

interface COERepositoryInterface{
    public function create(array $data): CenterOfExcellence;
    public function update(CenterOfExcellence $coe, array $data): CenterOfExcellence;
    public function delete(CenterOfExcellence $coe): void;

    public function findByid(int $id): CenterOfExcellence;
    public function findBySlug(string $slug): CenterOfExcellence;
    public function all();
}