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
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'indexable' => 'boolean',
        'price' => 'decimal:2'
    ];

    // Array form visibility
    protected $appends = ['image_url'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
    }

    // Accessor for image_url
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail');
    }
}
