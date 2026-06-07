<?php

namespace Modules\DOCTOR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Doctor extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'slug',
        'specialty',
        'qualifications',
        'designation',
        'institute',
        'biography',
        'chamber_location',
        'visit_fee',
        'report_fee',
        'phone',
        'email',
        'mis_code',
        'skills',
        'awards',
        'is_active',
        'is_home_page',
        'is_featured',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'indexable',
        'created_by',
        'updated_by',
    ];

    // MEDIA COLLECTIONS
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
            ->singleFile();

        $this->addMediaCollection('gallery');

        $this->addMediaCollection('certificates');
    }
}