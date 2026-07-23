<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\APPOINTMENT\Services\TimeSlotService;
use Modules\WEBSITE\Services\DoctorService;

final class AppointmentResolver implements DataResolverInterface
{
    public function __construct(
        private readonly AppointmentService $appointmentService,
        private readonly TimeSlotService    $timeSlotService,
        private readonly DoctorService      $doctorService,
    ) {}

    /**
     * Returns doctor list as context for the LLM booking flow.
     */
    public function resolve(string $keyword): array
    {
        try {
            $doctors = $this->doctorService->getAllDoctors();

            return $doctors->map(fn ($doctor) => [
                'doctor_id'   => $doctor->id,
                'name'        => $doctor->name,
                'designation' => $doctor->designation ?? 'Consultant',
                'department'  => $doctor->department->name ?? 'General',
                'visit_fee' => $doctor->visit_fee,
                'image_url'   => $doctor->image
                    ? (filter_var($doctor->image, FILTER_VALIDATE_URL)
                        ? $doctor->image
                        : asset(ltrim($doctor->profile_image_url, '/')))
                    : null,
                'schedule'    => $this->formatSchedule($doctor->timetables ?? []),
            ])->values()->all();
        } catch (\Throwable $e) {
            Log::error('[AppointmentResolver] Failed to load doctors', ['error' => $e->getMessage()]);
            return [['Notice' => 'ডাক্তারের তালিকা লোড করতে সমস্যা হয়েছে। অনুগ্রহ করে হটলাইনে কল করুন।']];
        }
    }

    /**
     * Create the appointment. Called only after LLM emits the action block
     * and AiAgentService parses it.
     *
     * Required: doctor_id, appointment_date, start_time, end_time,
     *           patient_name, patient_phone
     * Optional: patient_email, notes
     */
    public function book(array $data): array
    {
        // Validate required fields before hitting the service
        $required = ['doctor_id', 'appointment_date', 'start_time', 'end_time', 'patient_name', 'patient_phone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'error' => "Missing required field: {$field}"];
            }
        }

        // Derive slot_duration from start/end times
        if (empty($data['slot_duration'])) {
            try {
                $start = \Carbon\Carbon::createFromFormat('H:i', substr($data['start_time'], 0, 5));
                $end   = \Carbon\Carbon::createFromFormat('H:i', substr($data['end_time'], 0, 5));
                $data['slot_duration'] = (int) $start->diffInMinutes($end);
            } catch (\Throwable) {
                $data['slot_duration'] = 30; // safe default
            }
        }

        // Remove optional fields that are null/empty so the model doesn't choke
        foreach (['patient_email', 'notes'] as $opt) {
            if (empty($data[$opt])) unset($data[$opt]);
        }

        try {
            $appointment = $this->appointmentService->createAppointment($data);

            // Resolve doctor name for the confirmation reply
            $doctorName = null;
            try {
                $doctor     = $this->doctorService->getDoctorById((int) $appointment->doctor_id);
                $doctorName = $doctor?->name;
            } catch (\Throwable) {}

            return [
                'success'          => true,
                'appointment_id'   => $appointment->id,
                'patient_name'     => $appointment->patient_name,
                'patient_phone'    => $appointment->patient_phone,
                'patient_email'    => $appointment->patient_email,
                'doctor_name'      => $doctorName ?? "Doctor #{$appointment->doctor_id}",
                'appointment_date' => $appointment->appointment_date,
                'start_time'       => $appointment->start_time,
                'end_time'         => $appointment->end_time,
                'status'           => $appointment->status,
            ];
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            Log::error('[AppointmentResolver] Booking failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'অ্যাপয়েন্টমেন্ট বুক করতে সার্ভারে সমস্যা হয়েছে।'];
        }
    }

    public function getAvailableSlots(int $doctorId, string $date): array
    {
        try {
            return $this->timeSlotService->getAvailableSlots($doctorId, $date);
        } catch (\Throwable $e) {
            Log::error('[AppointmentResolver] Slot fetch failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function formatSchedule(iterable $timetables): string
    {
        $slots = [];
        foreach ($timetables as $slot) {
            $slots[] = "{$slot->day_of_week}: {$slot->start_time} – {$slot->end_time}";
        }
        return $slots ? implode(', ', $slots) : 'শিডিউল জানতে কল করুন';
    }
}
