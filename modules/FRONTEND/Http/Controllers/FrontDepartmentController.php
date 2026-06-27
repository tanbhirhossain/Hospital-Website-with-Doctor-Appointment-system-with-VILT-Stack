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
}