<?php

namespace Modules\DOCTOR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\DOCTOR\Models\Department;
use Modules\DOCTOR\Requests\StoreDepartmentRequest;
use Modules\DOCTOR\Services\DepartmentService;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $deptService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:departments,id'],
            'media' => ['nullable', 'string', 'in:with_media,without_media'],
        ]);

        return Inertia::render('DOCTOR::Department/Index', [
            'departments' => $this->deptService->getAllDepartments($filters)->through(fn (Department $department): array => [
                'id' => $department->id,
                'name' => $department->name,
                'slug' => $department->slug,
                'shortDesc' => $department->shortDesc,
                'descriptions' => $department->descriptions,
                'text_icon' => $department->text_icon,
                'parent_id' => $department->parent_id,
                'parent' => $department->parent ? [
                    'id' => $department->parent->id,
                    'name' => $department->parent->name,
                ] : null,
                'media' => [
                    'banner_image' => $department->getFirstMediaUrl('banner_image') ?: null,
                    'image' => $department->getFirstMediaUrl('image') ?: null,
                    'icon_image' => $department->getFirstMediaUrl('icon_image') ?: null,
                ],
                'created_at' => $department->created_at?->toFormattedDateString(),
            ]),
            'parentOptions' => $this->deptService->getParentOptions()
                ->map(fn (Department $department): array => [
                    'id' => $department->id,
                    'name' => $department->name,
                ])
                ->values(),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'parent_id' => isset($filters['parent_id']) ? (int) $filters['parent_id'] : null,
                'media' => $filters['media'] ?? '',
            ],
            'can' => [
                'create' => true,
                'edit' => true,
                'delete' => true,
            ],
        ]);

    }

    public function create(): Response
    {
        return Inertia::render('DOCTOR::Department/Create', [
            'parentOptions' => $this->deptService->getParentOptions()
                ->map(fn (Department $department): array => [
                    'id' => $department->id,
                    'name' => $department->name,
                ])
                ->values(),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->deptService->store($request->validated(), $request->user()->id);

        return to_route('departments.index')->with('success', 'Department created successfully.');
    }

    public function update(StoreDepartmentRequest $request, Department $department): RedirectResponse
    {
        $this->deptService->update($department->id, $request->validated());

        return to_route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->deptService->destroy($department->id);

        return to_route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
