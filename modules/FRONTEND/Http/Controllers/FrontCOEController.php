<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Services\COEService;

class FrontCOEController extends Controller
{
    public function __construct(
        private readonly COEService $coeService,
        private readonly COERepositoryInterface $coeRepo,
        
    ){}

    public function index()
    {
        $coes = $this->coeService->getAllCOEs();
        return inertia('FRONTEND::COE/COE', [
            'coes' => $coes,
        ]);
    }

    public function show(string $slug)
    {
        $coe = $this->coeRepo->findBySlug($slug);
        $coes = $this->coeService->getAllCOEs();
        return inertia('FRONTEND::COE/Details', [
            'coe' => $coe,
            'coes' => $coes,
        ]);
    }
}