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

    public function index()
    {
        $services = $this->serviceRepo->allUnpaginated(['is_active' => true]);

        // Map gallery URLs for each service
        $services = $services->map(function ($service) {
            $service->gallery_images = $service->getMedia('gallery')->map(fn ($m) => [
                'id'   => $m->id,
                'url'  => $m->getUrl(),
                'name' => $m->file_name,
            ])->toArray();

            $service->banner_url = $service->getFirstMediaUrl('banner') ?: $service->getFirstMediaUrl('thumbnail');
            $service->thumbnail_url = $service->getFirstMediaUrl('thumbnail');

            return $service;
        });

        return Inertia::render('FRONTEND::Services/Index', [
            'services' => $services,
        ]);
    }

    public function details(string $slug)
    {
        $service = $this->serviceRepo->findBySlug($slug);

        if (! $service) {
            abort(404, 'Service not found.');
        }

        // Load gallery, banner, thumbnail URLs on the service
        $service->gallery_images = $service->getMedia('gallery')->map(fn ($m) => [
            'id'   => $m->id,
            'url'  => $m->getUrl(),
            'name' => $m->file_name,
        ])->toArray();

        $service->banner_url = $service->getFirstMediaUrl('banner') ?: $service->getFirstMediaUrl('thumbnail');
        $service->thumbnail_url = $service->getFirstMediaUrl('thumbnail');

        $services = $this->serviceRepo->allUnpaginated(['is_active' => true]);
        $departments = $this->departmentRepo->allUnpaginated();

        return Inertia::render('FRONTEND::Services/Details', [
            'service'      => $service,
            'services'     => $services,
            'departments'  => $departments,
        ]);
    }
}
