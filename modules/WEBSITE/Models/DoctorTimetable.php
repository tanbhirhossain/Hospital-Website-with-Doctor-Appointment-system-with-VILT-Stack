<?php

namespace Modules\WEBSITE\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorTimetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'location',
        'is_active',
        'max_patient',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}