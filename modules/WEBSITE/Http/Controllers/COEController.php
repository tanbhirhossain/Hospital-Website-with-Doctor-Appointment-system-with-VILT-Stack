<?php

namespace Modules\WEBSITE\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\WEBSITE\Http\Requests\COERequest;
use Modules\WEBSITE\Models\CenterOfExcellence;
use Modules\WEBSITE\Services\COEService;

class COEController extends Controller
{
    protected COEService $service;

    public function __construct(COEService $service)
    {
        $this->service = $service;
    }

    public function index(): Response
    {
        return Inertia::render('WEBSITE::COE/Index', [
            'items' => $this->service->getAllCOEs()
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('WEBSITE::COE/Form', [
            'item' => null
        ]);
    }

    public function store(COERequest $request): RedirectResponse
    {
        $this->service->store($request->validated());
        return redirect()->route('admin.coe.index')->with('success', 'COE created successfully.');
    }

    public function edit(CenterOfExcellence $coe): Response
    {
        return Inertia::render('WEBSITE::COE/Form', [
            'item' => $coe
        ]);
    }

    public function update(COERequest $request, CenterOfExcellence $coe): RedirectResponse
    {
        $this->service->update($coe, $request->validated());
        return redirect()->route('admin.coe.index')->with('success', 'COE updated successfully.');
    }

    public function destroy(CenterOfExcellence $coe): RedirectResponse
    {
        $this->service->destroy($coe);
        return redirect()->route('admin.coe.index')->with('success', 'COE deleted successfully.');
    }

    public function show(CenterOfExcellence $coe): Response
    {
        return Inertia::render('COE/Show', [
            'item' => $coe
        ]);
    }
}