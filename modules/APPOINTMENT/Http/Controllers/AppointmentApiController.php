<?php

namespace Modules\APPOINTMENT\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\APPOINTMENT\Http\Requests\StoreAppointmentRequest;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\APPOINTMENT\Services\TimeSlotService;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;

class AppointmentApiController extends Controller
{
    public function __construct(
        private readonly AppointmentService  $appointmentService,
        private readonly DoctorTimetableRepositoryInterface $timetableRepo,
        private readonly DepartmentRepositoryInterface $departmentRepo,
        private readonly DoctorRepositoryInterface $doctorRepo,
        private readonly TimeSlotService $timeSlotService
    
    ) {}

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $startDate = $request->input('start_date', $request->input('date'));
        $endDate = $request->input('end_date', $startDate);

        if (!$startDate) {
            return response()->json([], 400);
        }

        $slots = $this->timeSlotService->getTimeSlotDateWise(
            (int) $request->doctor_id, 
            $startDate, 
            $endDate
        );

        if ($request->has('start_date')) {
            return response()->json($slots);
        }

        return response()->json($slots[$startDate] ?? []);
    }

    public function getDoctorAvailability(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = $this->timetableRepo->getDoctorAvilabilityDateByRange(
            (int) $request->doctor_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json($data);
    }

    public function bookAppointment(StoreAppointmentRequest $request)
    {
        try {
            $appointment = $this->appointmentService->createAppointment($request->all());
            return response()->json(['message' => 'Appointment booked successfully.', 'appointment' => $appointment], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getDoctors(){
        $doctors = $this->doctorRepo->allActive();

        return response()->json($doctors);
    }

    public function getDepartments(){
        $departments = $this->departmentRepo->allUnpaginated();
        $departments->load('doctors');
        return response()->json($departments);
    }

    
}