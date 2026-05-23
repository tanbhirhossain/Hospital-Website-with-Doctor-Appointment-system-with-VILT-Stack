<?php

namespace Modules\DOCTOR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;

class Department extends Model
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    protected $guarded = [];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')->singleFile();
        $this->addMediaCollection('image')->singleFile();
    }


}