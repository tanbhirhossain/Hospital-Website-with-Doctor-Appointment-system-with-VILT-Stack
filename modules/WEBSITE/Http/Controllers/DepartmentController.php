<?php

namespace Modules\WEBSITE\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE\Models\Department;
use Modules\WEBSITE\Requests\StoreDepartmentRequest;
use Modules\WEBSITE\Services\DepartmentService;

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
            'is_active' => ['nullable', 'boolean'],
        ]);

        return Inertia::render('WEBSITE::Department/Index', [
            'departments' => $this->deptService->getAllDepartments($filters)->through(fn (Department $department): array => [
                'id' => $department->id,
                'name' => $department->name,
                'slug' => $department->slug,
                'tagline' => $department->tagline,
                'shortDesc' => $department->shortDesc,
                'descriptions' => $department->descriptions,
                'serial' => $department->serial,
                'is_active' => $department->is_active,
                'text_icon' => $department->text_icon,
                'bg-color' => $department->{'bg-color'},
                'text-color' => $department->{'text-color'},
                'color' => $department->color,
                'custom_css' => $department->custom_css,
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
                'meta_title' => $department->meta_title,
                'meta_description' => $department->meta_description,
                'canonical_url' => $department->canonical_url,
                'og_title' => $department->og_title,
                'og_description' => $department->og_description,
                'indexable' => $department->indexable,
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
                'is_active' => $filters['is_active'] ?? null,
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
        return Inertia::render('WEBSITE::Department/Create', [
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

        return to_route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department): Response
    {
        $department->load('parent');

        return Inertia::render('WEBSITE::Department/Edit', [
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
                'slug' => $department->slug,
                'tagline' => $department->tagline,
                'shortDesc' => $department->shortDesc,
                'descriptions' => $department->descriptions,
                'serial' => $department->serial,
                'is_active' => $department->is_active,
                'text_icon' => $department->text_icon,
                'bg-color' => $department->{'bg-color'},
                'text-color' => $department->{'text-color'},
                'color' => $department->color,
                'custom_css' => $department->custom_css,
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
                'meta_title' => $department->meta_title,
                'meta_description' => $department->meta_description,
                'canonical_url' => $department->canonical_url,
                'og_title' => $department->og_title,
                'og_description' => $department->og_description,
                'indexable' => $department->indexable,
                'created_at' => $department->created_at?->toFormattedDateString(),
            ],
            'parentOptions' => $this->deptService->getParentOptions()
                ->map(fn (Department $dept): array => [
                    'id' => $dept->id,
                    'name' => $dept->name,
                ])
                ->values(),
        ]);
    }

    public function update(StoreDepartmentRequest $request, Department $department): RedirectResponse
    {
        $this->deptService->update($department->id, $request->validated());

        return to_route('admin.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->deptService->destroy($department->id);

        return to_route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }
}