<?php
namespace Modules\WEBSITE_EXTRA\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class ClientReview extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['client_name','client_designation','client_company','review_text','rating','sort_order','is_active','is_featured','created_by','updated_by'];
    protected $casts = ['is_active'=>'boolean','is_featured'=>'boolean','rating'=>'integer'];
    public function registerMediaCollections(): void {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
