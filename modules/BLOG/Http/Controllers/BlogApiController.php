<?php

namespace Modules\BLOG\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\BLOG\Models\Blog;
use Modules\BLOG\Services\BlogService;

class BlogApiController extends Controller
{
    public function __construct(private readonly BlogService $blogs) {}

    public function index(Request $request): JsonResponse
    {
        $blogs = $this->blogs->paginate($request->only(['search', 'department']), $request->integer('per_page', 10))
            ->through(fn (Blog $blog): array => $this->serialize($blog, false));

        return response()->json($blogs);
    }

    public function show(string $slug): JsonResponse
    {
        return response()->json(['data' => $this->serialize($this->blogs->findBySlug($slug), true)]);
    }

    /** @return array<string, mixed> */
    private function serialize(Blog $blog, bool $withContent): array
    {
        return array_filter([
            'name' => $blog->name,
            'slug' => $blog->slug,
            'excerpt' => str(strip_tags($blog->descriptions))->squish()->limit(180)->toString(),
            'descriptions' => $withContent ? $blog->descriptions : null,
            'department' => $blog->department?->only(['name', 'slug']),
            'meta_title' => $blog->meta_title,
            'meta_description' => $blog->meta_description,
            'canonical_url' => $blog->canonical_url,
            'og_image_url' => $blog->og_image_url,
            'is_indexable' => $blog->is_indexable,
            'created_at' => $blog->created_at?->toISOString(),
        ], fn (mixed $value) => $value !== null);
    }
}
