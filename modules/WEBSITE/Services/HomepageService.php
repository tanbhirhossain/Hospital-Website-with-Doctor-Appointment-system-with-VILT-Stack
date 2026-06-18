<?php

namespace Modules\WEBSITE\Services;

use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;

class HomepageService
{
    public function __construct(
        private DoctorRepositoryInterface $drRepo,
        private DepartmentRepositoryInterface $deptRepo,
        private COERepositoryInterface $coeRepo
    ){}

    public function doctor_list(){
        $list = $this->drRepo->all();
    }
}