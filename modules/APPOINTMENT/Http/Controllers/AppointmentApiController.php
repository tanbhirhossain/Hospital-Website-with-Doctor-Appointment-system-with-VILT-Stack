<?php

namespace Modules\APPOINTMENT\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\WEBSITE\Interfaces\DoctorTimetableRepositoryInterface;

class AppointmentApiController extends Controller
{
    public function __construct(
        private readonly AppointmentService  $appointmentService,
        private readonly DoctorTimetableRepositoryInterface $timetableRepo
    
    ) {}

    public function getAvailableSlots(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $slots = $this->appointmentService->getAvailableSlots($doctorId, $request->date);

        return response()->json($slots);
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
}