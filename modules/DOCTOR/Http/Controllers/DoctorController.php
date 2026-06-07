<?php

namespace Modules\DOCTOR\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\DOCTOR\Interfaces\DepartmentRepositoryInterface;
use Modules\DOCTOR\Services\DepartmentService;

class DoctorController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ){}

    public function index(){

    }

    public function create(){
        $departments = $this->departmentService->getAllDepartments();
        return Inertia::render('DOCTOR::Doctor/Create', [
            'departments' => is_array($departments) ? array_values($departments) : $departments->values()
        ]);
    }

    
}