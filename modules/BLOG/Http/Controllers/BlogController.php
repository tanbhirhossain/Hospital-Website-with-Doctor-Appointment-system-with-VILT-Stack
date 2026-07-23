<?php

namespace Modules\BLOG\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\BLOG\Models\Blog;
use Modules\BLOG\Requests\StoreBlogRequest;
use Modules\BLOG\Services\BlogService;
use Modules\WEBSITE\Models\Department;

class BlogController extends Controller
{
    public function __construct(private readonly BlogService $blogs) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('blog.view'), 403);
        $filters = $request->only(['search', 'department', 'indexable', 'trashed']);

        return Inertia::render('BLOG::Index', [
            'blogs' => $this->blogs->paginate($filters)->through(fn (Blog $blog): array => $this->serialize($blog)),
            'departments' => Department::query()->select(['id', 'name'])->orderBy('name')->get(),
            'filters' => $filters,
            'can' => [
                'create' => $request->user()?->can('blog.create') === true,
                'edit' => $request->user()?->can('blog.edit') === true,
                'delete' => $request->user()?->can('blog.delete') === true,
                'restore' => $request->user()?->can('blog.restore') === true,
            ],
        ]);
    }

    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $this->blogs->create(
            $request->safe()->except(['og_image_file']),
            (int) $request->user()->id,
            $request->file('og_image_file'),
        );

        return to_route('blogs.index')->with('success', 'Blog created successfully.');
    }

    public function update(StoreBlogRequest $request, Blog $blog): RedirectResponse
    {
        $this->blogs->update(
            $blog,
            $request->safe()->except(['og_image_file']),
            (int) $request->user()->id,
            $request->file('og_image_file'),
        );

        return back()->with('success', 'Blog updated successfully.');
    }

    public function destroy(Request $request, Blog $blog): RedirectResponse
    {
        abort_unless($request->user()?->can('blog.delete'), 403);
        $this->blogs->delete($blog);

        return back()->with('success', 'Blog moved to trash.');
    }

    public function restore(Request $request, int $blog): RedirectResponse
    {
        abort_unless($request->user()?->can('blog.restore'), 403);
        $this->blogs->restore($this->blogs->find($blog, true));

        return back()->with('success', 'Blog restored successfully.');
    }

    public function forceDelete(Request $request, int $blog): RedirectResponse
    {
        abort_unless($request->user()?->can('blog.delete'), 403);
        $this->blogs->forceDelete($this->blogs->find($blog, true));

        return back()->with('success', 'Blog permanently deleted.');
    }

    /** @return array<string, mixed> */
    private function serialize(Blog $blog): array
    {
        return [
            'id' => $blog->id,
            'name' => $blog->name,
            'slug' => $blog->slug,
            'department_id' => $blog->department_id,
            'department' => $blog->department?->only(['id', 'name', 'slug']),
            'descriptions' => $blog->descriptions,
            'meta_title' => $blog->meta_title,
            'meta_description' => $blog->meta_description,
            'canonical_url' => $blog->canonical_url,
            'og_image' => $blog->og_image,
            'og_image_url' => $blog->og_image_url,
            'is_indexable' => $blog->is_indexable,
            'creator' => $blog->creator?->name,
            'updater' => $blog->updater?->name,
            'created_at' => $blog->created_at?->toFormattedDateString(),
            'updated_at' => $blog->updated_at?->diffForHumans(),
            'deleted_at' => $blog->deleted_at?->toISOString(),
        ];
    }
}
