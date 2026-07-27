<?php

namespace Modules\SITE_SETTINGS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuickCard extends Model
{
    protected $fillable = ['title', 'link', 'icon', 'gradient', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
