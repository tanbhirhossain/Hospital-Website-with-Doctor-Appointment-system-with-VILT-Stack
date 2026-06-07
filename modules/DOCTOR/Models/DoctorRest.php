<?php

namespace Modules\DOCTOR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorRest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'doctor_id',
        'start_date',
        'end_date',
        'reason',
        'type',
        'is_active',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}