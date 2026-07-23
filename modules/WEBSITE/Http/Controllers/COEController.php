<?php

namespace Modules\WEBSITE\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE\Http\Requests\COEStoreRequest;
use Modules\WEBSITE\Http\Requests\COEUpdateRequest;
use Modules\WEBSITE\Models\CenterOfExcellence;
use Modules\WEBSITE\Services\COEService;
use App\Http\Controllers\Controller;

class COEController extends Controller
{
    protected COEService $service;

    public function __construct(COEService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $coes = $this->service->getAllCOEs();

        return Inertia::render('WEBSITE::COE/Index', [
            'coes' => $coes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('WEBSITE::COE/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(COEStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->store($data);

        return redirect()->route('admin.coe.index')
            ->with('success', 'Center of Excellence created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CenterOfExcellence $coe): Response
    {
        // Load media if needed
        $coe->load('media');

        return Inertia::render('WEBSITE::COE/Edit', [
            'coe' => $coe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(COEUpdateRequest $request, CenterOfExcellence $coe): RedirectResponse
    {
        $data = $request->validated();
        $this->service->update($coe, $data);

        return redirect()->route('admin.coe.index')
            ->with('success', 'Center of Excellence updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CenterOfExcellence $coe): RedirectResponse
    {
        $this->service->destroy($coe);

        return redirect()->route('admin.coe.index')
            ->with('success', 'Center of Excellence deleted successfully.');
    }
}