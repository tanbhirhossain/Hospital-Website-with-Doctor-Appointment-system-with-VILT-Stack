<?php

namespace Modules\SITE_SETTINGS\Models;

use Illuminate\Database\Eloquent\Model;

class WhyChooseUsItem extends Model
{
    protected $fillable = ['title', 'description', 'icon', 'gradient', 'color', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
