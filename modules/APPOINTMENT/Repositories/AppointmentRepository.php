<?php 

namespace Modules\APPOINTMENT\Repositories;

use Carbon\Carbon;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Models\Appointment;
use Override;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    #[Override]
    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    #[Override]
    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);
        return $appointment;
    }

    #[Override]
    public function delete(Appointment $appointment): void
    {
        $appointment->delete();
    }

    #[Override]
    public function changeStaus(Appointment $appointment, string $status)
    {
        $appointment->update(['status' => $status]);
        return $appointment;
    }

    #[Override]
    public function confirmBooking(Appointment $appointment)
    {
        // স্ট্যাটাস confirmed এবং মাইগ্রেশনের confirmed_at ফিল্ডটি টাইমস্ট্যাম্প সহ আপডেট হবে
        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => Carbon::now()
        ]);
        return $appointment;
    }

    #[Override]
    public function cancelBooking(Appointment $appointment)
    {
        // কন্ট্রোলার বা রিকোয়েস্ট থেকে cancellation_reason পাস করার ব্যবস্থা রাখা ভালো 
        // এখানে বেসিক স্ট্যাটাস আপডেট করা হলো
        $appointment->update([
            'status' => 'cancelled'
        ]);
        return $appointment;
    }

    #[Override]
    public function findById(int $id)
    {
        return Appointment::find($id);
    }

    #[Override]
    public function findByDoctor(int $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    #[Override]
    public function findByPatient(int $phone)
    {
        // ইন্টারফেস অনুযায়ী ফোন নম্বর ইন্টিজার হিসেবে রিসিভ করা হচ্ছে
        return Appointment::where('patient_phone', $phone)
            ->orderBy('appointment_date', 'desc')
            ->get();
    }

    #[Override]
    public function checkTimeSlotAvailability(int $doctorId, string $date, string $startTime)
    {
        $exists = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereTime('start_time', $startTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        return !$exists;
    }

    #[Override]
    public function getBookedSlotsByDateRange(int $doctorId, string $fromDate, string $toDate)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$fromDate, $toDate])
            ->where('status', '!=', 'cancelled')
            ->get(['appointment_date', 'start_time']);
    }

    #[Override]
    public function dayWiseListByDr(Carbon $date, int $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date->format('Y-m-d'))
            ->orderBy('start_time', 'asc')
            ->get();
    }

    #[Override]
    public function dateRangeListByDr(Carbon $fromDate, Carbon $toDate, int $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    #[Override]
    public function pendingList($filters = [])
    {
        return Appointment::where('status', 'pending')
            ->when(isset($filters['doctor_id']), function($query) use ($filters) {
                return $query->where('doctor_id', $filters['doctor_id']);
            })
            ->when(isset($filters['date']), function($query) use ($filters) {
                return $query->whereDate('appointment_date', $filters['date']);
            })
            ->orderBy('appointment_date', 'asc')
            ->get();
    }

    #[Override]
    public function canclledList($filters = [])
    {
        return Appointment::where('status', 'cancelled')
            ->when(isset($filters['doctor_id']), function($query) use ($filters) {
                return $query->where('doctor_id', $filters['doctor_id']);
            })
            ->orderBy('appointment_date', 'desc')
            ->get();
    }

    #[Override]
    public function confirmedList($filters = [])
    {
        return Appointment::where('status', 'confirmed')
            ->when(isset($filters['doctor_id']), function($query) use ($filters) {
                return $query->where('doctor_id', $filters['doctor_id']);
            })
            ->orderBy('appointment_date', 'asc')
            ->get();
    }
}
