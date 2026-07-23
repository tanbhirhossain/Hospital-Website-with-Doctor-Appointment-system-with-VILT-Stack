<?php

namespace Modules\BLOG\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\BLOG\Models\Blog;
use Modules\BLOG\Services\BlogService;
use Modules\WEBSITE\Models\Department;

class PublicBlogController extends Controller
{
    public function __construct(private readonly BlogService $blogs) {}

    public function index(Request $request): Response
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'department' => $request->input('department'),
        ];

        return Inertia::render('BLOG::PublicIndex', [
            'blogs' => $this->blogs->paginate($filters, 9)->through(fn (Blog $blog): array => [
                'name' => $blog->name,
                'slug' => $blog->slug,
                'excerpt' => str(strip_tags($blog->descriptions))->squish()->limit(180)->toString(),
                'department' => $blog->department?->only(['id', 'name', 'slug']),
                'og_image_url' => $blog->og_image_url,
                'created_at' => $blog->created_at?->toFormattedDateString(),
            ]),
            'departments' => Department::query()
                ->select(['departments.id', 'departments.name'])
                ->whereIn('departments.id', Blog::query()->select('department_id')->distinct())
                ->addSelect(['blogs_count' => Blog::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('department_id', 'departments.id')])
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): Response
    {
        $blog = $this->blogs->findBySlug($slug);

        return Inertia::render('BLOG::Show', [
            'blog' => [
                'name' => $blog->name,
                'slug' => $blog->slug,
                'descriptions' => $blog->descriptions,
                'department' => $blog->department?->only(['name', 'slug']),
                'meta_title' => $blog->meta_title ?: $blog->name,
                'meta_description' => $blog->meta_description,
                'canonical_url' => $blog->canonical_url ?: route('blog.show', $blog->slug),
                'og_image_url' => $blog->og_image_url,
                'is_indexable' => $blog->is_indexable,
                'author' => $blog->creator?->name,
                'created_at' => $blog->created_at?->toFormattedDateString(),
            ],
        ]);
    }
}
