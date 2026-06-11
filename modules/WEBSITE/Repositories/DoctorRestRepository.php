<?php 

namespace Modules\WEBSITE\Repositories;

use Modules\WEBSITE\Interfaces\DoctorRestRepositoryInterface;

class DoctorRestRepository implements DoctorRestRepositoryInterface{
    public function create(array $data): \Modules\WEBSITE\Models\DoctorRest
    {
        return \Modules\WEBSITE\Models\DoctorRest::create($data);
    }

    public function update(\Modules\WEBSITE\Models\DoctorRest $rests, array $data): \Modules\WEBSITE\Models\DoctorRest
    {
        $rests->update($data);
        return $rests;
    }

    public function delete(\Modules\WEBSITE\Models\DoctorRest $rests): void
    {
        $rests->delete();
    }


    public function findByid(int $id): \Modules\WEBSITE\Models\DoctorRest
    {
        return \Modules\WEBSITE\Models\DoctorRest::findOrFail($id);
    }

    public function findByDrIdAndDate(int $drid, \Carbon\Carbon $date): \Modules\WEBSITE\Models\DoctorRest
    {
        return \Modules\WEBSITE\Models\DoctorRest::where('dr_id', $drid)
            ->whereDate('date', $date->toDateString())
            ->firstOrFail();
    }

    public function findByDrId(int $drid): \Illuminate\Support\Collection
    {
        return \Modules\WEBSITE\Models\DoctorRest::where('dr_id', $drid)->get();
    }


    public function all()
    {
        return \Modules\WEBSITE\Models\DoctorRest::all();
    }
    
}
    
