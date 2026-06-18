<?php

namespace Modules\WEBSITE\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class HomepageController extends Controller
{
    public function __construct(
        private DoctorRepositoryInterface $drRepo,
        private DepartmentRepositoryInterface $deptRepo,
        private COERepositoryInterface $coeRepo
    ){}

    public function index(){
        
        // $data = [
        //      'doctors' => $this->drRepo->allHomePageDoctor(),
        //     'departments' => $this->deptRepo->list_for_home_page(),
        //     'centers' => $this->coeRepo->all()    
        
        // ];

        // dd($data);

        return Inertia::render('WEBSITE::Home',[
            'doctors' => $this->drRepo->allHomePageDoctor(),
            'departments' => $this->deptRepo->list_for_home_page(),
            'centers' => $this->coeRepo->all()    
        ]);
    }
    
}