<?php 

namespace Modules\DOCTOR\Interfaces;

use Modules\DOCTOR\Models\Doctor;

interface DoctorRepositoryInterface{
    public function create(array $data): Doctor;
    public function update(Doctor $doctor, array $data): Doctor;
    public function delete(Doctor $doctor): void;

    public function findByid(int $id): Doctor;
    public function findBySlug(string $slug): Doctor;
    public function all();

    
}