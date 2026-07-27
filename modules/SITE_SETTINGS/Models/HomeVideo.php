<?php

namespace Modules\SITE_SETTINGS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeVideo extends Model
{
    protected $fillable = ['title', 'video_url', 'video_type', 'thumbnail_path', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail_path) return null;
        if (str_starts_with($this->thumbnail_path, 'http')) return $this->thumbnail_path;
        return Storage::disk('public')->url($this->thumbnail_path);
    }
}
