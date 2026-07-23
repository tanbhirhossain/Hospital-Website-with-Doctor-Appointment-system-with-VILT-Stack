<?php

namespace Modules\WEBSITE\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;

class ApiController extends Controller
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepo
    ){}

    public function departments(){
        return $this->departmentRepo->listForNavigation();
    }
}