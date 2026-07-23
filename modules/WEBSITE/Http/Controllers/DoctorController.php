<?php

namespace Modules\WEBSITE\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE\Http\Requests\DoctorStoreRequest;
use Modules\WEBSITE\Http\Requests\DoctorUpdateRequest;
use Modules\WEBSITE\Models\Doctor;
use Modules\WEBSITE\Services\DepartmentService;
use Modules\WEBSITE\Services\DoctorService;



class DoctorController extends Controller
{
    // High-level modules do not depend on low-level modules (ORM). Both depend on abstractions.
    public function __construct(
        protected DoctorService $doctorService,
        protected DepartmentService $departmentService
    ) {}

    public function index(): Response
    {
        return Inertia::render('WEBSITE::Index', [
            'doctors' => $this->doctorService->getAllDoctors(),
        ]);
    }

    public function create(): Response
    {
        $departments = $this->departmentService->getAllDepartments();

        return Inertia::render('WEBSITE::Doctor/Create', [
            'departments' => is_array($departments) ? array_values($departments) : $departments->values(),
        ]);
    }

    public function store(DoctorStoreRequest $request): RedirectResponse
    {
        $this->doctorService->storeWithTimetables($request->validated());

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor created successfully.');
    }

    public function edit(Doctor $doctor): Response
    {
        $doctor = $this->doctorService->getDoctorById($doctor->id);

        return Inertia::render('WEBSITE::Doctor/Edit', [
            'doctor' => $doctor,
            'departments' => $this->doctorService->getDepartmentOptions(),
            'profileImage' => $doctor->getMedia('profile_image')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->file_name,
            ])->first(),
            'gallery' => $doctor->getMedia('gallery')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->file_name,
            ]),
            'certificates' => $doctor->getMedia('certificates')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->file_name,
            ]),
        ]);
    }

    public function update(DoctorUpdateRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->doctorService->updateDoctorById($doctor->id, $request->validated());

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $this->doctorService->deleteDoctorById($doctor->id);

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }
}
