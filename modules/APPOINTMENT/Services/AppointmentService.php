<?php 

namespace Modules\APPOINTMENT\Services;

use Exception;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Models\Appointment;

class AppointmentService {
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepo
    ){}

    /**
     * নতুন অ্যাপয়েন্টমেন্ট তৈরি করা এবং অটোমেটিক সিরিয়াল নাম্বার বসানো।
     */
    public function createAppointment(array $data): Appointment
    {
        // ১. বুক করার আগে একই স্লট ইতিমধ্যে অন্য কেউ বুক করেছে কিনা তা ভ্যালিডেট করা
        $isAvailable = $this->appointmentRepo->checkTimeSlotAvailability(
            $data['doctor_id'], 
            $data['appointment_date'], 
            $data['start_time']
        );

        if (!$isAvailable) {
            throw new Exception('দুঃখিত, এই টাইম স্লটটি ইতিমধ্যে বুকড হয়ে গেছে!');
        }

        // ২. অটোমেটিক সিরিয়াল নাম্বার (Serial Number) জেনারেশন লজিক
        // নির্দিষ্ট তারিখে ওই ডক্টরের আজকের সর্বোচ্চ সিরিয়াল কত তা চেক করা
        $lastAppointment = Appointment::where('doctor_id', $data['doctor_id'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->orderBy('serial_number', 'desc')
            ->first();

        // নতুন সিরিয়াল হবে পূর্বের সিরিয়ালের সাথে + ১ যোগ
        $data['serial_number'] = $lastAppointment ? ($lastAppointment->serial_number + 1) : 1;
        $data['serial_mode'] = $data['serial_mode'] ?? 'auto'; // ডিফল্ট auto মুড

        return $this->appointmentRepo->create($data);
    }

    /**
     * অ্যাপয়েন্টমেন্টের তথ্য আপডেট করা।
     */
    public function updateAppointment(int $id, array $data): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        return $this->appointmentRepo->update($appointment, $data);
    }

    /**
     * অ্যাপয়েন্টমেন্ট সম্পূর্ণ ডিলিট করা।
     */
    public function deleteAppointment(int $id): void
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        $this->appointmentRepo->delete($appointment);
    }

    /**
     * ডাইনামিক স্ট্যাটাস চেঞ্জ করা (pending, completed, no_show ইত্যাদি)।
     */
    public function changeStatus(int $id, string $status): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        return $this->appointmentRepo->changeStaus($appointment, $status);
    }

    /**
     * অ্যাপয়েন্টমেন্ট কনফার্ম করা (স্ট্যাটাস এবং confirmed_at টাইমস্ট্যাম্প আপডেট)।
     */
    public function confirmBooking(int $id): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        return $this->appointmentRepo->confirmBooking($appointment);
    }

    /**
     * অ্যাপয়েন্টমেন্ট ক্যানসেল বা বাতিল করা।
     */
    public function cancelBooking(int $id, ?string $reason = null): Appointment
    {
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            throw new Exception('অ্যাপয়েন্টments খুঁজে পাওয়া যায়নি!');
        }

        // ক্যানসেলেশন রিজন থাকলে তা আগে মডেলে অ্যাসাইন করা, তারপর রিপোজিটরি কল
        if ($reason) {
            $appointment->cancellation_reason = $reason;
        }

        return $this->appointmentRepo->cancelBooking($appointment);
    }
}
