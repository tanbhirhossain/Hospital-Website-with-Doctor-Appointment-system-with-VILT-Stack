<?php

namespace Modules\WEBSITE_EXTRA\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\ServiceRequest;
use Modules\WEBSITE_EXTRA\Services\ServiceService;

class ServiceController extends Controller
{
    public function __construct(protected ServiceService $service) {}

    public function index(Request $request): Response
    {
        return Inertia::render('WEBSITE_EXTRA::Service/Index', [
            'services' => $this->service->paginate($request->only(['search'])),
            'filters'  => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('WEBSITE_EXTRA::Service/Create');
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $this->service->store($request->validated(), Auth::id());
        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Accept either {service} or {id} route parameter.
     */
    public function edit($id): Response
    {
        $service = $this->service->findById((int) $id);

        return Inertia::render('WEBSITE_EXTRA::Service/Edit', [
            'service'        => $service,
            'thumbnail'      => $service->getFirstMedia('thumbnail')
                ? ['id' => $service->getFirstMedia('thumbnail')->id, 'url' => $service->getFirstMediaUrl('thumbnail'), 'name' => $service->getFirstMedia('thumbnail')->file_name]
                : null,
            'banner'         => $service->getFirstMedia('banner')
                ? ['id' => $service->getFirstMedia('banner')->id, 'url' => $service->getFirstMediaUrl('banner'), 'name' => $service->getFirstMedia('banner')->file_name]
                : null,
            'gallery_images' => $service->getMedia('gallery')->map(fn ($m) => [
                'id'   => $m->id,
                'url'  => $m->getUrl(),
                'name' => $m->file_name,
            ])->toArray(),
        ]);
    }

    public function update(ServiceRequest $request, $id): RedirectResponse
    {
        $this->service->update((int) $id, $request->validated(), Auth::id());
        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $this->service->destroy((int) $id);
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
