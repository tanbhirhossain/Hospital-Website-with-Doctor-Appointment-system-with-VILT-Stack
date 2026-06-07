<?php 

namespace Modules\DOCTOR\Interfaces;

use Illuminate\Support\Collection;
use Modules\DOCTOR\Models\DoctorTimetable;

interface DoctorTimetableRepositoryInterface{
    public function create(array $data): DoctorTimetable;
    public function update(DoctorTimetable $timetable, array $data): DoctorTimetable;
    public function delete(DoctorTimetable $timetable): void;

    public function findByDrId(int $dr_id): Collection;
    public function all();
}