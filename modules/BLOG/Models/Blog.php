<?php

namespace Modules\BLOG\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\WEBSITE\Models\Department;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'department_id',
        'descriptions',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image',
        'is_indexable',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['og_image_url'];

    protected function casts(): array
    {
        return ['is_indexable' => 'boolean'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn (Builder $builder, string $term) => $builder
            ->where(fn (Builder $inner) => $inner
                ->where('name', 'like', "%{$term}%")
                ->orWhere('descriptions', 'like', "%{$term}%")));
    }

    public function getOgImageUrlAttribute(): ?string
    {
        if (! $this->og_image) {
            return null;
        }

        return str_starts_with($this->og_image, 'http://') || str_starts_with($this->og_image, 'https://')
            ? $this->og_image
            : Storage::disk('public')->url($this->og_image);
    }
}
