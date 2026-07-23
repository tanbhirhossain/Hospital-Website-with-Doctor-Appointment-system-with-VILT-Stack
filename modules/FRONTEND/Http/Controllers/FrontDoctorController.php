<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class FrontDoctorController extends Controller
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepo
    ){}

    public function index(){
        // dd($this->doctorRepo->allActive());
        return Inertia::render('FRONTEND::Doctors/Doctors', [
            'doctors' => $this->doctorRepo->allActive()
        ]);
    }

    public function profile($slug){
        return Inertia::render('FRONTEND::Doctors/Details', [
            'doctor' => $this->doctorRepo->findBySlug($slug)
        ]);
    }
}