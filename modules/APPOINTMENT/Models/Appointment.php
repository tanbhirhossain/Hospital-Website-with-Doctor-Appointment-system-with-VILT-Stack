<?php

namespace Modules\APPOINTMENT\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\WEBSITE\Models\Doctor;

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

protected $casts = [
    'appointment_date' => 'date',
    'start_time'       => 'datetime:H:i',
    'end_time'         => 'datetime:H:i',
    'slot_duration'    => 'integer',
    'serial_number'    => 'integer',
    'confirmed_at'     => 'datetime',
    'created_at'       => 'datetime',
    'updated_at'       => 'datetime',
];

    /* ------------------------------------------------------------------ */
    /*  Status Constants                                                    */
    /* ------------------------------------------------------------------ */

    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW   = 'no_show';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_NO_SHOW   => 'No Show',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                       */
    /* ------------------------------------------------------------------ */

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->where('appointment_date', $date);
    }

    public function scopeByDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }
}
