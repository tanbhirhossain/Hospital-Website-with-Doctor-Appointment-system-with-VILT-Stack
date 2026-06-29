<?php

namespace Modules\WEBSITE\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\WEBSITE\Models\Department;
use Modules\WEBSITE\Models\DoctorTimetable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Doctor extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

     
    protected $appends = ['profile_image_url'];
    




    // MEDIA COLLECTIONS
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
            ->singleFile();

        $this->addMediaCollection('gallery');

        $this->addMediaCollection('certificates');
    }

    public function getProfileImageUrlAttribute(){
        return $this->getFirstMediaUrl('profile_image');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function timetables()
    {
        return $this->hasMany(DoctorTimetable::class);
    }
}
