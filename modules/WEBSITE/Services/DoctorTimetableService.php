<?php 

namespace Modules\WEBSITE\Services;

use Illuminate\Support\Facades\Auth;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;
use Modules\WEBSITE\Models\DoctorTimetable;

class DoctorTimetableService{

    public function __construct(
        protected DoctorTimetableRepositoryInterface $timetableRepo
    ){}


    public function getAllTimeTables(){
        return $this->timetableRepo->all();
    }

    public function getTimetablesByDoctor(int $doctorId){
        return $this->timetableRepo->findByDrId($doctorId);
    }

    public function store(array $data) { 

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        return $this->timetableRepo->create($data);
    }

    public function update(DoctorTimetable $timetable, array $data){
        $data['updated_by'] = Auth::id();
        return $this->timetableRepo->update($timetable, $data);
    }

    public function delete(DoctorTimetable $timetable):void{
        $this->timetableRepo->delete($timetable);
    }

}