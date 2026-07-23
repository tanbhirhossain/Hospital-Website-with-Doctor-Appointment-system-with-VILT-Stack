<?php

namespace Modules\GALLERY\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GalleryItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'alt_text',
        'is_published',
        'is_featured',
        'sort_order',
        'published_at',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['image_url', 'thumb_url'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the main image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('gallery');
    }

    /**
     * Get the thumb conversion URL.
     */
    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('gallery', 'thumb');
    }

    // ... existing methods (category, scopePublished, registerMediaCollections, etc)
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(fn (Builder $builder) => $builder
                ->whereNull('published_at')
                ->orWhere('published_at', '<=', now()));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->useDisk(config('media-library.disk_name', 'public'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 480, 360)
            ->quality(82)
            ->performOnCollections('gallery');

        $this->addMediaConversion('preview')
            ->fit(Fit::Max, 1600, 1200)
            ->quality(88)
            ->performOnCollections('gallery');
    }
}