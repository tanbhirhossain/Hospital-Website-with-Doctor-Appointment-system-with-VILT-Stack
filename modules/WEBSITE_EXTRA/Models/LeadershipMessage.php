<?php

namespace Modules\WEBSITE_EXTRA\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LeadershipMessage extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name', 'role', 'role_line', 'eyebrow', 'title', 'quote',
        'credentials', 'photo_path', 'message', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    protected $appends = ['photo_url'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        // Prefer Spatie media if available
        $media = $this->getFirstMediaUrl('photo');
        if ($media) return $media;

        // Fallback to photo_path
        if (! $this->photo_path) return null;
        if (str_starts_with($this->photo_path, 'http')) return $this->photo_path;
        return Storage::disk('public')->url($this->photo_path);
    }
}
