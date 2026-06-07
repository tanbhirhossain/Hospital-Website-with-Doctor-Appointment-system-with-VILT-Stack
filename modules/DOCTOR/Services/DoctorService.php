<?php 

namespace Modules\DOCTOR\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\DOCTOR\Interfaces\DoctorRepositoryInterface;
use Modules\DOCTOR\Interfaces\DoctorTimetableRepositoryInterface;
use Modules\DOCTOR\Models\Doctor;

class DoctorService {
    public function __construct(
        protected DoctorRepositoryInterface $doctorRepo,
        protected DoctorTimetableRepositoryInterface $timetableRepo
    ){}

    public function getAllDoctors(){
        return $this->doctorRepo->all();
    }

    public function getDoctorById(int $id): Doctor{
        return $this->doctorRepo->findByid($id);
    }

    public function getDoctorBySlug(string $slug): Doctor{
        return $this->doctorRepo->findBySlug($slug);
    }

    public function storeWithTimetables(array $data): Doctor {

        return DB::transaction(function () use ($data){
            if(empty($data['slug'])){
                $data['slug'] = Str::slug($data['name']);
            }

            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $doctor = $this->doctorRepo->create($data);

            if(!empty($data['timetables']) && is_array($data['timetables'])){
                foreach($data['timetables'] as $timetableData){
                    $timetableData['doctor_id'] = $doctor->id;
                    $timetableData['created_by'] = Auth::id();
                    $timetableData['updated_by'] = Auth::id();

                    $this->timetableRepo->create($timetableData);
                }
            }

            return $doctor->load('timetables');
            
        });
    }

    public function updateWithTimetables(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data) {
            
            // ১. ডক্টরের ডাটা আপডেট করা
            if (!empty($data['name']) && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            $data['updated_by'] = Auth::id();
            
            $doctor = $this->doctorRepo->update($doctor, $data);

            // ২. যদি নতুন টাইমটেবিল ডাটা পাঠানো হয়, তবে আপডেট করা
            if (isset($data['timetables']) && is_array($data['timetables'])) {
                

                $oldTimetables = $this->timetableRepo->findByDrId($doctor->id);
                foreach ($oldTimetables as $oldTimetable) {
                    $this->timetableRepo->delete($oldTimetable);
                }

                foreach ($data['timetables'] as $timetableData) {
                    $timetableData['doctor_id'] = $doctor->id;
                    $timetableData['created_by'] = Auth::id();
                    $timetableData['updated_by'] = Auth::id();

                    $this->timetableRepo->create($timetableData);
                }
            }

            return $doctor->load('timetables');
        });
    }


    public function delete(Doctor $doctor): void
    {
        $this->doctorRepo->delete($doctor);
    }





}