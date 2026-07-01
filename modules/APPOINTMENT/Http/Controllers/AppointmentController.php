<?php

namespace Modules\APPOINTMENT\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\APPOINTMENT\Http\Requests\StoreAppointmentRequest;
use Modules\APPOINTMENT\Http\Requests\UpdateAppointmentRequest;
use Modules\APPOINTMENT\Interfaces\AppointmentRepositoryInterface;
use Modules\APPOINTMENT\Services\AppointmentService;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;
use Modules\WEBSITE\Models\Doctor;

class AppointmentController extends Controller
{
    private const STATUSES = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
        'completed' => 'Completed',
        'no_show'   => 'No Show',
    ];

    public function __construct(
        private readonly AppointmentService             $appointmentService,
        private readonly AppointmentRepositoryInterface $appointmentRepo,
        private readonly DoctorRepositoryInterface      $doctorRepository,
    ) {}

    public function index(): Response
    {
        $filters = request()->only(['status', 'doctor_id', 'date', 'date_from', 'date_to', 'search']);
        
        // আমাদের রেপোজিটরির ফিল্টার মেথড অনুযায়ী পেন্ডিং, কনফার্মড বা ক্যানসেলড লিস্ট লোড করা
        $status = request('status', 'pending');
        if ($status === 'confirmed') {
            $appointments = $this->appointmentRepo->confirmedList($filters);
        } elseif ($status === 'cancelled') {
            $appointments = $this->appointmentRepo->canclledList($filters);
        } else {
            $appointments = $this->appointmentRepo->pendingList($filters);
        }

    $doctors = Doctor::with(['timetables' => fn ($q) => $q->where('is_active', true)])->get();

    return Inertia::render('APPOINTMENT::Appointments/Index', [
        'appointments' => $appointments,
        'doctors'      => $doctors,
        'statuses'     => self::STATUSES,
        'filters'      => $filters,
    ]);
    }

    public function create(): Response
    {
        return Inertia::render('APPOINTMENT::Appointments/Create', [
            'doctors'  => $this->doctorRepository->all(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        try {
            $this->appointmentService->createAppointment($request->validated());
            return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id): Response
    {
        $appointment = $this->appointmentRepo->findById($id);

        if (!$appointment) {
            abort(404, 'Appointment not found');
        }

        return Inertia::render('APPOINTMENT::Appointments/Show', [
            'appointment' => $appointment->load('doctor'), // রিলেশন লোড
            'statuses'    => self::STATUSES,
        ]);
    }

    public function edit(int $id): Response
    {
        $appointment = $this->appointmentRepo->findById($id);

        if (!$appointment) {
            abort(404, 'Appointment not found');
        }

        return Inertia::render('APPOINTMENT::Appointments/Edit', [
            'appointment' => $appointment,
            'doctors'     => $this->doctorRepository->all(),
            'statuses'    => self::STATUSES,
        ]);
    }

    public function update(UpdateAppointmentRequest $request, int $id)
    {
        try {
            $this->appointmentService->updateAppointment($id, $request->validated());
            return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->appointmentService->deleteAppointment($id);
            return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function confirm(int $id)
    {
        try {
            $this->appointmentService->confirmBooking($id);
            return redirect()->back()->with('success', 'Appointment confirmed.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, int $id)
    {
        try {
            $reason = $request->input('cancellation_reason');
            $this->appointmentService->cancelBooking($id, $reason);
            return redirect()->back()->with('success', 'Appointment cancelled.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function complete(int $id)
    {
        try {
            $this->appointmentService->changeStatus($id, 'completed');
            return redirect()->back()->with('success', 'Appointment completed.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function noShow(int $id)
    {
        try {
            $this->appointmentService->changeStatus($id, 'no_show');
            return redirect()->back()->with('success', 'Marked as no-show.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
        ]);

        // You already have the service logic! Use it here.
        // Note: You need to inject TimeSlotService into the controller
        $slots = $this->timeSlotService->getTimeSlotDateWise(
            $request->doctor_id, 
            $request->date, 
            $request->date
        );

        return response()->json($slots[$request->date] ?? []);
    }
}
