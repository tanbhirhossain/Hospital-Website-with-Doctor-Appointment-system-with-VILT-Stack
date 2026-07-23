<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class FrontAppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('FRONTEND::Appointment',[
            'availableWeekdays' => [0, 1, 2, 3, 4, 6],
            'blockedDates' => []
        ]);
    }
}