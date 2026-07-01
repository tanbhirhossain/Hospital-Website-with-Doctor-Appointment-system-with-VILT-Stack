<?php 

namespace Modules\APPOINTMENT\Interfaces;

use Carbon\Carbon;
use Modules\APPOINTMENT\Models\Appointment;

interface AppointmentRepositoryInterface{
    public function create(array $data):Appointment;
    public function update(Appointment $appointment, array $data):Appointment;
    public function delete(Appointment $appointment): void;

    public function changeStaus(Appointment $appointment, string $status);
    public function confirmBooking(Appointment $appointment);
    public function cancelBooking(Appointment $appointment);

    public function findById(int $id);
    public function findByDoctor(int $doctorId);
    public function findByPatient(int $phone);

    public function checkTimeSlotAvailability(int $doctorId, string $date, string $startTime);
    public function getBookedSlotsByDateRange(int $doctorId, string $fromDate, string $toDate);
    
    public function dayWiseListByDr(Carbon $date, int $doctorId);
    public function dateRangeListByDr(Carbon $fromDate, Carbon $toDate, int $doctorId);

    public function pendingList($filters = []);
    public function canclledList($filters = []);
    public function confirmedList($filters = []);


    

}