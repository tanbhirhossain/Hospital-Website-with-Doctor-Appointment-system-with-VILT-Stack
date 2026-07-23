<?php 

namespace Modules\WEBSITE\Repositories;

use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Models\CenterOfExcellence;
use Override;

class COERepository implements COERepositoryInterface{
    public function create(array $data): CenterOfExcellence
    {
        return CenterOfExcellence::create($data);
    }
    
    public function update(CenterOfExcellence $coe, array $data): CenterOfExcellence
    {
        $coe->update($data);
        return $coe;
    }

    public function delete(CenterOfExcellence $coe): void
    {
        $coe->delete();
    }

    public function findByid(int $id): CenterOfExcellence
    {
        return CenterOfExcellence::findOrFail($id);
    }

    public function findBySlug(string $slug): CenterOfExcellence
    {
        return CenterOfExcellence::where('slug', $slug)->first();
    }

    public function all()
    {
        return CenterOfExcellence::with('media')->get();
    }

    public function allActive()
    {
        return CenterOfExcellence::with('media')->where('is_active', true)->get();
    }

    #[Override]
    public function listForHome()
    {
        return CenterOfExcellence::with('media')->take(6)->get();
    }

}