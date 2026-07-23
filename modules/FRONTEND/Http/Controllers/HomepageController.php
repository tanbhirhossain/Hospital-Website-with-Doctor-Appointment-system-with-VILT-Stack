<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\BLOG\Services\BlogService;
use Modules\GALLERY\Interfaces\GalleryItemRepositoryInterface;
use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\HeroSliderRepositoryInterface;

class HomepageController extends Controller
{
    public function __construct(
        private DoctorRepositoryInterface $drRepo,
        private DepartmentRepositoryInterface $deptRepo,
        private COERepositoryInterface $coeRepo,
        private BlogService $blogService,
        private HeroSliderRepositoryInterface $slideRepo,
        private GalleryItemRepositoryInterface $galleryRepo
    ){}

    public function index(){
        


    $slides = $this->slideRepo->allforHomepage();

    foreach ($slides as $slide) {
        if (is_string($slide->buttons)) {
            $cleanJson = str_replace(["\r\n", "\r", "\n"], '', $slide->buttons);
            $cleanJson = stripslashes($cleanJson); 
            $slide->buttons = json_decode($cleanJson, true);
        }
    }

        // dd($this->coeRepo->all());
        return Inertia::render('FRONTEND::Home',[
            'doctors' => $this->drRepo->allHomePageDoctor(),
            'departments' => $this->deptRepo->list_for_home_page(),
            'centers' => $this->coeRepo->listForHome(),
            'blogs' => $this->blogService->latestThree(),
            'slides' => $this->slideRepo->allforHomepage(),
            'galleries' => $this->galleryRepo->allforHome()
        ]);


    }
    
}