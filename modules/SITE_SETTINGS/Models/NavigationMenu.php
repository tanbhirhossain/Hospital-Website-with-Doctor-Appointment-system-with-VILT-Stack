<?php

namespace Modules\SITE_SETTINGS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    protected $fillable = [
        'label', 'url', 'route_name', 'icon', 'target',
        'parent_id', 'sort_order', 'is_active', 'location',
        'menu_type', 'config', 'badge_text', 'badge_color', 'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config'    => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavigationMenu::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeHeader($query)
    {
        return $query->where('location', 'header');
    }

    public function scopeFooter($query)
    {
        return $query->where('location', 'footer');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Build a nested tree for the given location.
     */
    public static function tree(string $location = 'header'): \Illuminate\Support\Collection
    {
        $all = self::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $roots = $all->whereNull('parent_id');

        return $roots->map(function ($root) use ($all) {
            $root->children = $all->where('parent_id', $root->id)->values();
            return $root;
        })->values();
    }
}
