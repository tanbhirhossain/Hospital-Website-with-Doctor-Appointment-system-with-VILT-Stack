<?php

namespace Modules\APPOINTMENT\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\APPOINTMENT\Http\Requests\CheckAvailabilityRequest;
use Modules\APPOINTMENT\Http\Requests\StoreAppointmentRequest;
use Modules\APPOINTMENT\Interfaces\AppointmentServiceInterface;
use Modules\APPOINTMENT\Interfaces\TimeSlotServiceInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class BookingController extends Controller
{
    public function __construct(
        private readonly AppointmentServiceInterface $appointmentService,
        private readonly TimeSlotServiceInterface    $timeSlotService,
        private readonly DoctorRepositoryInterface   $doctorRepository,
    ) {}

    /**
     * Show the public booking page.
     */
    public function index(): Response
    {
        $doctors = $this->doctorRepository->all();

        return Inertia::render('APPOINTMENT::Booking/Index', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Get available time slots for a doctor on a specific date (API endpoint).
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,id',
            'date'      => 'required|date|after_or_equal:today',
        ]);

        try {
            $slots = $this->timeSlotService->getAvailableSlots(
                (int) $request->doctor_id,
                $request->date,
            );

            return response()->json([
                'success' => true,
                'slots'   => $slots,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new appointment from the public booking form.
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $this->appointmentService->createAppointment($request->validated());

            return redirect()
                ->route('booking.index')
                ->with('success', 'Your appointment has been booked successfully! We will confirm it shortly.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
