<?php
namespace Modules\WEBSITE_EXTRA\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class HealthPackage extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['name','slug','badge','short_description','description','includes','original_price','discounted_price','duration_days','category','sort_order','is_active','is_featured','meta_title','meta_description','indexable','created_by','updated_by'];
    protected $casts = ['is_active'=>'boolean','is_featured'=>'boolean','indexable'=>'boolean','original_price'=>'decimal:2','discounted_price'=>'decimal:2'];
    public function registerMediaCollections(): void {
        $this->addMediaCollection('thumbnail')->singleFile();
    }
    public function getDiscountPercentageAttribute(): ?int {
        if ($this->original_price > 0 && $this->discounted_price) {
            return (int) round((($this->original_price - $this->discounted_price) / $this->original_price) * 100);
        }
        return null;
    }
}
