<?php
namespace Modules\WEBSITE_EXTRA\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class HeroSlider extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['title','subtitle','description','button_text','button_url','button_text_secondary','button_url_secondary','badge_text','sort_order','is_active','created_by','updated_by'];
    protected $casts = ['is_active' => 'boolean'];
    public function registerMediaCollections(): void {
        $this->addMediaCollection('slide_image')->singleFile();
    }
}
