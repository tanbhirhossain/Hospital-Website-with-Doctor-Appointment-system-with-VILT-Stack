<?php

namespace Modules\APPOINTMENT\Support;

/**
 * Appointment status constants — shared across the module.
 * No Model dependency needed.
 */
class AppointmentStatus
{
    public const PENDING   = 'pending';
    public const CONFIRMED = 'confirmed';
    public const CANCELLED = 'cancelled';
    public const COMPLETED = 'completed';
    public const NO_SHOW   = 'no_show';

    public static function all(): array
    {
        return [
            self::PENDING   => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::NO_SHOW   => 'No Show',
        ];
    }

    public static function keys(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::CANCELLED,
            self::COMPLETED,
            self::NO_SHOW,
        ];
    }
}
