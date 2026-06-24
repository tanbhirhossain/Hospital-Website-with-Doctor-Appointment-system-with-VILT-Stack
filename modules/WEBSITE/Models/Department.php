<?php

namespace Modules\WEBSITE\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\WEBSITE\Database\Factories\DepartmentFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Department extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = ['name', 'slug', 'text_icon','tagline', 'shortDesc', 'descriptions', 'parent_id', 'created_by'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')->singleFile();
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('icon_image')->singleFile();
    }

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }
}
