<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\APPOINTMENT\Services\TimeSlotService;
use Modules\WEBSITE\Services\DoctorService;

/**
 * Handles the full multi-turn appointment booking flow.
 *
 * The LLM drives the conversation — it reads the history and decides what
 * information is still missing. When all required fields are collected, it
 * returns a special JSON action block that this resolver intercepts to
 * actually create the appointment via AppointmentService.
 *
 * Required fields: doctor_id, appointment_date, start_time, end_time,
 *                  patient_name, patient_phone
 */
final class AppointmentResolver implements DataResolverInterface
{
    public function __construct(
        private readonly AppointmentService $appointmentService,
        private readonly TimeSlotService    $timeSlotService,
        private readonly DoctorService      $doctorService,
    ) {}

    /**
     * $keyword carries the raw user message for this turn (set by AiAgentService).
     * The actual multi-turn orchestration happens in AgentPrompts + the LLM reply.
     * This resolver provides structured context data (available doctors + slots)
     * that the LLM reply prompt uses to guide the conversation.
     */
    public function resolve(string $keyword): array
    {
        // Provide the list of all doctors so the LLM can present choices
        try {
            $doctors = $this->doctorService->getAllDoctors();

            return $doctors->map(fn ($doctor) => [
                'doctor_id'   => $doctor->id,
                'name'        => $doctor->name,
                'designation' => $doctor->designation ?? 'Consultant',
                'department'  => $doctor->department->name ?? 'General',
                'schedule'    => $this->formatSchedule($doctor->timetables ?? []),
            ])->values()->all();
        } catch (\Throwable $e) {
            Log::error('[AppointmentResolver] Failed to load doctors', ['error' => $e->getMessage()]);
            return [['Notice' => 'ডাক্তারের তালিকা লোড করতে সমস্যা হয়েছে। অনুগ্রহ করে হটলাইনে কল করুন।']];
        }
    }

    /**
     * Called by AiAgentService when the LLM signals that all booking details
     * have been collected and confirmed by the patient.
     *
     * @param  array{doctor_id: int, appointment_date: string, start_time: string,
     *                end_time: string, patient_name: string, patient_phone: string,
     *                patient_email?: string, notes?: string} $data
     */
    public function book(array $data): array
    {
        try {
            $appointment = $this->appointmentService->createAppointment($data);

            return [
                'success'          => true,
                'appointment_id'   => $appointment->id,
                'patient_name'     => $appointment->patient_name,
                'doctor_id'        => $appointment->doctor_id,
                'appointment_date' => $appointment->appointment_date,
                'start_time'       => $appointment->start_time,
                'end_time'         => $appointment->end_time,
                'slot_duration'    => \Carbon\Carbon::parse($appointment->start_time)->diffInMinutes(\Carbon\Carbon::parse($appointment->end_time)) ?? null,
                'status'           => $appointment->status,
            ];
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            Log::error('[AppointmentResolver] Booking failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'অ্যাপয়েন্টমেন্ট বুক করতে সার্ভারে সমস্যা হয়েছে।'];
        }
    }

    /**
     * Fetch available time slots for a given doctor and date.
     */
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
