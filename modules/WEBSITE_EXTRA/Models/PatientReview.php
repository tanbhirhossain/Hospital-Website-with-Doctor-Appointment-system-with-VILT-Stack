<?php
namespace Modules\WEBSITE_EXTRA\Models;
use Illuminate\Database\Eloquent\Model;
use Modules\WEBSITE\Models\Doctor;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class PatientReview extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['patient_name','patient_phone','doctor_id','department','review_text','rating','status','is_featured','admin_note','created_by','updated_by'];
    protected $casts = ['is_featured'=>'boolean','rating'=>'integer'];
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function registerMediaCollections(): void {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
