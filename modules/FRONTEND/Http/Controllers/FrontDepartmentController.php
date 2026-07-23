<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;

class FrontDepartmentController extends Controller
{
    public function __construct(
        private DepartmentRepositoryInterface $departRepo
    ){}
    public function index(){
        
        return Inertia::render('FRONTEND::Departments/Departments',[
            'departments' => $this->departRepo->allUnpaginated()
        ]);
    }

    public function details($slug){
        // dd($this->departRepo->findBySlug($slug));
        return Inertia::render('FRONTEND::Departments/Details',[
            'currentDepartment' => $this->departRepo->findBySlug($slug),
            'departments' => $this->departRepo->allUnpaginated(),
            
        ]);
    }
}