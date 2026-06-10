<?php

namespace Modules\WEBSITE\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CenterOfExcellence extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

   protected $fillable = [
        'name', 'slug', 'description', 'icon', 'style',
        'created_by', 'updated_by', 'meta_title', 
        'meta_description', 'canonical_url', 'og_title', 
        'og_description', 'indexable'
    ];

    protected $casts = [
        'indexable' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'System']);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault(['name' => 'System']);
    }

     // MEDIA COLLECTIONS
    public function registerMediaCollections(): void{
        $this->addMediaCollection('banner_image')->singleFile();
        $this->addMediaCollection('thumb_image')->singleFile();
        $this->addMediaCollection('gallery');

    }
}