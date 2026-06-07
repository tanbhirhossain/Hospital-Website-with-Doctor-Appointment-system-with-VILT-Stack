<?php 

namespace Modules\DOCTOR\Repositories;

use Modules\DOCTOR\Interfaces\DoctorRepositoryInterface;
use Modules\DOCTOR\Models\Doctor;
use Override;

class DoctorRepository implements DoctorRepositoryInterface{
    #[Override]
    public function create(array $data): Doctor
    {
        return Doctor::create($data);
    }

    #[Override]
    public function update(Doctor $doctor, array $data): Doctor
    {
         $doctor->update($data);
         return $doctor;
    }

    #[Override]
    public function delete(Doctor $doctor): void
    {
        $doctor->delete();
    }


    #[Override]
    public function findByid(int $id): Doctor
    {
        return Doctor::findOrFail($id);
    }

    #[Override]
    public function findBySlug(string $slug): Doctor
    {
        return Doctor::where('slug', $slug)->first();  
    
    }

    #[Override]
    public function all()
    {
        return Doctor::all();
    }
}