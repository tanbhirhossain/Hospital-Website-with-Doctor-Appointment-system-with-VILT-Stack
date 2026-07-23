<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\ServiceRepositoryInterface;

class FrontServiceController extends Controller
{
    public function __construct(
        private ServiceRepositoryInterface $serviceRepo,
        private DepartmentRepositoryInterface $departmentRepo
    ){}

    public function index(){
        $services = $this->serviceRepo->allUnpaginated();
        // dd($services);
        return Inertia::render('FRONTEND::Services/Index', [
            'services' => $services
        ]); 
    }

    public function details(string $slug){

        $service = $this->serviceRepo->findBySlug($slug);
        $services = $this->serviceRepo->allUnpaginated();
        $departments = $this->departmentRepo->allUnpaginated();

        return Inertia::render('FRONTEND::Services/Details', [
            'service' => $service,
            'services' => $services,
            'departments' => $departments
        ]); 
    }
}