<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Modules\BLOG\Interfaces\BlogRepositoryInterface;
use Modules\BLOG\Services\BlogService;

class FrontBlogController extends Controller
{
    public function __construct(
        private BlogRepositoryInterface $blogRepo,
        private BlogService $blogService
    ){}

    public function index(){
        return Inertia::render('FRONTEND::Blog/Index', [
            'blogs' => $this->blogRepo->allActive(),
            
        ]);
    }

    public function details($slug){
        $blog = $this->blogRepo->findBySlug($slug);

        return Inertia::render('FRONTEND::Blog/Details', [
            'blog' => $blog
        ]);
    }

}