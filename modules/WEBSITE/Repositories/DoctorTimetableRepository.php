<?php 

namespace Modules\WEBSITE\Repositories;

use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;
use Modules\WEBSITE\Models\DoctorTimetable;
use Override;

class DoctorTimetableRepository implements DoctorTimetableRepositoryInterface{
    #[Override]
    public function create(array $data): DoctorTimetable
    {
       return DoctorTimetable::create($data);
    }

    #[Override]
    public function update(DoctorTimetable $timetable, array $data): DoctorTimetable
    {
        $timetable->update($data);
        return $timetable;
    }

    #[Override]
    public function delete(DoctorTimetable $timetable): void
    {
        $timetable->delete();
    }

    #[Override]
    public function findByDrId(int $dr_id): Collection
    {
        return DoctorTimetable::where('doctor_id', $dr_id)->get();
    }

    #[Override]
    public function findByDayAndDrId(int $day, int $dr_id)
    {
        return DoctorTimetable::where('day_of_week', $day)->where('doctor_id', $dr_id)->get();
    }

    #[Override]
    public function all()
    {
        return DoctorTimetable::all();
    }

    public function getDoctorAvilabilityDateByRange(int $doctorId, string $startDate, string $endDate){
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = array_map(fn($date) => $date->format('Y-m-d'), iterator_to_array($period));
        return $dates;

    }

}