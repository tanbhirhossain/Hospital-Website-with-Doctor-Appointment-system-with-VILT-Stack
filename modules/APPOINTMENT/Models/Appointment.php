<?php

namespace Modules\APPOINTMENT\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'appointment_date',
        'start_time',
        'end_time',
        'slot_duration',
        'status',
        'notes',
        'cancellation_reason',

        'serial_number',
        'appointment_serial',
        'serial_mode',
        'confirmed_at',
    ];

}