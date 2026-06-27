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

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'shortDesc',
        'descriptions',
        'serial',
        'is_active',
        
        // Styling
        'text_icon',
        'bg-color',
        'text-color',
        'color',
        'custom_css',
        
        // SEO
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'indexable',
        
        // Relationships / System tracking
        'parent_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'serial' => 'integer'
    ];

    protected $appends = ['banner_url', 'striped_desc', 'specialists'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function getSpecialistsAttribute()
    {
        return $this->doctors_count ?? $this->doctors()->count();   
    }

    
    public function getStripedDescAttribute()
    {
        return strip_tags($this->descriptions);
    }

    public function getBannerUrlAttribute()
    {
        return $this->getFirstMediaUrl('banner_image') ?: 'https://amzhospitalbd.com/storage/AMZ.jpg';
    }

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
