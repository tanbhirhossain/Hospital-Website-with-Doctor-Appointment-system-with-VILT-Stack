<?php

namespace Modules\WEBSITE_EXTRA\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title', 'slug', 'icon', 'short_description', 'description',
        'price', 'duration_minutes', 'sort_order', 'is_active',
        'is_featured', 'meta_title', 'meta_description', 'indexable',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
        'indexable'        => 'boolean',
        'price'            => 'decimal:2',
        'duration_minutes' => 'integer',
        'sort_order'       => 'integer',
    ];

    // Array form visibility
    protected $appends = ['image_url', 'gallery_urls'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
        $this->addMediaCollection('gallery'); // multiple images allowed
    }

    /**
     * Accessor for thumbnail URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail');
    }

    /**
     * Accessor for all gallery image URLs.
     */
    public function getGalleryUrlsAttribute(): array
    {
        return $this->getMedia('gallery')->map(fn ($media) => [
            'id'   => $media->id,
            'url'  => $media->getUrl(),
            'name' => $media->file_name,
        ])->toArray();
    }
}
