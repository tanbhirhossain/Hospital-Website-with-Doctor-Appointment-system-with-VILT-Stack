<?php

namespace Modules\SITE_SETTINGS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CorporatePartner extends Model
{
    protected $fillable = ['name', 'logo_path', 'website_url', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) return null;
        if (str_starts_with($this->logo_path, 'http')) return $this->logo_path;
        return Storage::disk('public')->url($this->logo_path);
    }
}
