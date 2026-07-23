<?php

namespace Modules\APPOINTMENT\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Appointment extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_age',
        'patient_gender',
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