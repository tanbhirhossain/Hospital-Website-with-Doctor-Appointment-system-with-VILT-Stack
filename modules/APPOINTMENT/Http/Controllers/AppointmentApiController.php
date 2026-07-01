<?php

namespace Modules\APPOINTMENT\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Modules\APPOINTMENT\Services\AppointmentService;

class AppointmentApiController extends Controller
{
    public function __construct(private readonly AppointmentService  $appointmentService) {}

    public function getAvailableSlots(Request $request, $doctorId)
    {
        $date = $request->query('date');
        return response()->json($this->appointmentService->getAvailableSlots((int)$doctorId, $date));
    }
}