<?php 

namespace Modules\WEBSITE\Repositories;

use Carbon\Carbon;
use Modules\WEBSITE\Interfaces\DoctorRestRepositoryInterface;
use Modules\WEBSITE\Models\DoctorRest;
use Override;

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

    #[Override]

    public function findByDrIdAndDateRange(int $drId, Carbon $startDate, Carbon $endDate)
    {
        return DoctorRest::where('doctor_id', $drId)
            ->where('is_active', true) // সাধারণত একটিভ রেস্টগুলোই খোঁজা হয়
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->get();
    }
    
}
    
