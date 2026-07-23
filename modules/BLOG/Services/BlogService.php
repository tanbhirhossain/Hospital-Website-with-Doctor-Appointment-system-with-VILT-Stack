<?php

namespace Modules\BLOG\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\BLOG\Interfaces\BlogRepositoryInterface;
use Modules\BLOG\Models\Blog;

class BlogService
{
    public function __construct(
        private readonly BlogRepositoryInterface $blogs,
        
        ) {}

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->blogs->paginate($filters, $perPage);
    }

    public function find(int $id, bool $withTrashed = false): Blog
    {
        return $this->blogs->find($id, $withTrashed);
    }

    public function findBySlug(string $slug): Blog
    {
        return $this->blogs->findBySlug($slug);
    }

    public function create(array $data, int $userId, ?UploadedFile $ogImage = null): Blog
    {
        return DB::transaction(function () use ($data, $userId, $ogImage): Blog {
            $data['slug'] = $data['slug'] ?: $this->uniqueSlug($data['name']);
            $data['created_by'] = $userId;
            $data['updated_by'] = null;

            if ($ogImage) {
                $data['og_image'] = $ogImage->store('blogs/og-images', 'public');
            }

            return $this->blogs->create($data)->load(['department', 'creator']);
        });
    }

    public function update(Blog $blog, array $data, int $userId, ?UploadedFile $ogImage = null): Blog
    {
        return DB::transaction(function () use ($blog, $data, $userId, $ogImage): Blog {
            $oldImage = $blog->og_image;
            $data['slug'] = $data['slug'] ?: $this->uniqueSlug($data['name'], $blog->id);
            $data['updated_by'] = $userId;

            if ($ogImage) {
                $data['og_image'] = $ogImage->store('blogs/og-images', 'public');
            }

            $updated = $this->blogs->update($blog, $data);

            if ($ogImage && $this->isLocalImage($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return $updated;
        });
    }

    public function delete(Blog $blog): bool
    {
        return DB::transaction(fn () => $this->blogs->delete($blog));
    }

    public function restore(Blog $blog): bool
    {
        if (! $blog->trashed()) {
            throw ValidationException::withMessages(['blog' => 'This blog is not in the trash.']);
        }

        return DB::transaction(fn () => $this->blogs->restore($blog));
    }

    public function forceDelete(Blog $blog): bool
    {
        if (! $blog->trashed()) {
            throw ValidationException::withMessages(['blog' => 'Move this blog to the trash before permanently deleting it.']);
        }

        return DB::transaction(function () use ($blog): bool {
            $image = $blog->og_image;
            $deleted = $this->blogs->forceDelete($blog);

            if ($deleted && $this->isLocalImage($image)) {
                Storage::disk('public')->delete($image);
            }

            return $deleted;
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'blog';
        $slug = $base;
        $counter = 2;

        while (Blog::withTrashed()->where('slug', $slug)->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function isLocalImage(?string $path): bool
    {
        return filled($path) && ! str_starts_with($path, 'http://') && ! str_starts_with($path, 'https://');
    }

    public function latestThree(){
        $data = $this->blogs->latestThree();

        return $data;
    }
}
