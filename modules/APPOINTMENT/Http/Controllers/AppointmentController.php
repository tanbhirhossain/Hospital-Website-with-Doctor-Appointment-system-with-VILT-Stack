<?php

namespace Modules\APPOINTMENT\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\APPOINTMENT\Http\Requests\StoreAppointmentRequest;
use Modules\APPOINTMENT\Http\Requests\UpdateAppointmentRequest;
use Modules\APPOINTMENT\Interfaces\AppointmentServiceInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

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
        private readonly AppointmentServiceInterface $appointmentService,
        private readonly DoctorRepositoryInterface   $doctorRepository,
    ) {}


    public function index(): Response
    {
        $filters = request()->only(['status', 'doctor_id', 'date', 'date_from', 'date_to', 'search']);
        $perPage = (int) request('per_page', 15);

        $appointments = $this->appointmentService->listAppointments($perPage, $filters);
        $doctors      = $this->doctorRepository->all();

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
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()>with('error', $e->getMessage());
        }
    }


    public function show(int $id): Response
    {
        $appointment = $this->appointmentService->getAppointment($id);

        return Inertia::render('APPOINTMENT::Appointments/Show', [
            'appointment' => $appointment->load('doctor'),
            'statuses'    => self::STATUSES,
        ]);
    }


    public function edit(int $id): Response
    {
        return Inertia::render('APPOINTMENT::Appointments/Edit', [
            'appointment' => $this->appointmentService->getAppointment($id),
            'doctors'     => $this->doctorRepository->all(),
            'statuses'    => self::STATUSES,
        ]);
    }


    public function update(UpdateAppointmentRequest $request, int $id)
    {
        try {
            $appointment = $this->appointmentService->getAppointment($id);
            $this->appointmentService->updateAppointment($appointment, $request->validated());
            return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function destroy(int $id)
    {
        $appointment = $this->appointmentService->getAppointment($id);
        $this->appointmentService->deleteAppointment($appointment);
        return redirect() ->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }


public function confirm(int $id)
{
    request()->validate([
        'serial_number' => ['nullable', 'integer', 'min:1'],
    ]);

    try {
        $appointment = $this->appointmentService->getAppointment($id);

        $this->appointmentService->confirmAppointment(
            $appointment,
            request()->filled('serial_number') ? (int) request('serial_number') : null
        );

        return redirect()->back()->with('success', 'Appointment confirmed.');
    } catch (\InvalidArgumentException $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    public function cancel(int $id)
    {
        try {
            $this->appointmentService->cancelAppointment(
                $this->appointmentService->getAppointment($id),
                request('cancellation_reason'),
            );

            return redirect()->back()->with('success', 'Appointment cancelled.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function complete(int $id)
    {
        try {
            $this->appointmentService->completeAppointment(
                $this->appointmentService->getAppointment($id)
            );

            return redirect()->back()->with('success', 'Appointment completed.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function noShow(int $id)
    {
        try {
            $this->appointmentService->markNoShow(
                $this->appointmentService->getAppointment($id)
            );

            return redirect()->back()->with('success', 'Marked as no-show.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
