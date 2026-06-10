<?php 

namespace Modules\WEBSITE\Interfaces;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\WEBSITE\Models\DoctorRest;

interface DoctorRestRepositoryInterface{
    public function create(array $data): DoctorRest;
    public function update(DoctorRest $rests, array $data): DoctorRest;
    public function delete(DoctorRest $rests): void;

    public function findByid(int $id): DoctorRest;
    public function findByDrIdAndDate(int $drid, Carbon $date): DoctorRest;
    public function findByDrId(int $drid): Collection;

    public function all();
}