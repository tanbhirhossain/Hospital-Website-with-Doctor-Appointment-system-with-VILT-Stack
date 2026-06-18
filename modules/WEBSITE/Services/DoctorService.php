<?php

namespace Modules\WEBSITE\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;
use Modules\WEBSITE\Models\Doctor;


class DoctorService
{
    public function __construct(
        protected DoctorRepositoryInterface $doctorRepo,
        protected DoctorTimetableRepositoryInterface $timetableRepo,
        protected DepartmentRepositoryInterface $departmentRepo
    ) {}

    public function getAllDoctors()
    {
        return $this->doctorRepo->all();
    }

    public function getDepartmentOptions()
    {
        return $this->departmentRepo->all();
    }

    public function getDoctorById(int $id): Doctor
    {
        $doctor = $this->doctorRepo->findByid($id);

        return $doctor->load('timetables');
    }

    public function storeWithTimetables(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $doctor = $this->doctorRepo->create(Arr::except($data, ['gallery', 'profile_image', 'certificates']));

            $this->syncMedia($doctor, $data);

            if (! empty($data['timetables']) && is_array($data['timetables'])) {
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

    public function updateDoctorById(int $id, array $data): Doctor
    {
        $doctor = $this->doctorRepo->findByid($id);

        return DB::transaction(function () use ($doctor, $data) {
            if (! empty($data['name']) && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            $data['updated_by'] = Auth::id();

            $doctor = $this->doctorRepo->update($doctor, Arr::except($data, ['gallery', 'profile_image', 'certificates']));

            $this->syncMedia($doctor, $data);

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

    public function deleteDoctorById(int $id): void
    {
        $doctor = $this->doctorRepo->findByid($id);
        $this->doctorRepo->delete($doctor);
    }

    private function syncMedia(Doctor $doctor, array $data): Doctor
    {
        if (($data['profile_image'] ?? null) instanceof UploadedFile) {
            $doctor->addMedia($data['profile_image'])->toMediaCollection('profile_image');
        }

        foreach ($data['gallery'] ?? [] as $galleryItem) {
            if ($galleryItem instanceof UploadedFile) {
                $doctor->addMedia($galleryItem)->toMediaCollection('gallery');
            }
        }

        foreach ($data['certificates'] ?? [] as $certificateItem) {
            if ($certificateItem instanceof UploadedFile) {
                $doctor->addMedia($certificateItem)->toMediaCollection('certificates');
            }
        }

        return $doctor->refresh();
    }

    
}
