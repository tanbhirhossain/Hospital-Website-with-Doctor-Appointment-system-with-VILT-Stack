<?php

namespace Modules\SITE_SETTINGS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        // Topbar
        'topbar_phone', 'topbar_email', 'topbar_notice', 'topbar_emergency',
        // Logo
        'logo_path', 'favicon_path', 'footer_logo_path',
        // About Us Page
        'about_title', 'about_content', 'about_image', 'about_video_url', 'about_video_title',
        // Contact Us Page
        'contact_title', 'contact_content', 'contact_map_embed',
        // Contact Information
        'contact_phone_primary', 'contact_phone_secondary', 'contact_email_primary',
        'contact_email_secondary', 'contact_hotline', 'contact_address', 'contact_city',
        // Footer
        'footer_description', 'footer_phone', 'footer_email', 'footer_address',
        'footer_facebook', 'footer_twitter', 'footer_linkedin', 'footer_youtube',
        'footer_instagram', 'footer_copyright',
        // Home sections
        'quick_cards_title',
        'home_about_title', 'home_about_content', 'home_about_video_url', 'home_about_image',
        'why_choose_title', 'why_choose_subtitle',
        'services_title', 'services_subtitle',
        'partners_title', 'partners_subtitle',
        // Section order
        'section_order',
    ];

    protected function casts(): array
    {
        return [
            'section_order' => 'array',
        ];
    }

    protected $appends = [
        'logo_url', 'favicon_url', 'footer_logo_url', 'about_image_url', 'home_about_image_url',
    ];

    /**
     * Get the singleton settings instance (creates one if not exists).
     */
    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'section_order' => json_encode([
                    'hero_slider',
                    'quick_cards',
                    'about_us',
                    'about_videos',
                    'why_choose_us',
                    'departments',
                    'centers_of_excellence',
                    'healthcare_services',
                    'doctors',
                    'leadership',
                    'gallery',
                    'health_packages',
                    'appointment',
                    'testimonials',
                    'stats',
                    'blog',
                    'corporate_partners',
                    'contact',
                ]),
            ]
        );
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) return null;
        return $this->makeStorageUrl($this->logo_path);
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if (! $this->favicon_path) return null;
        return $this->makeStorageUrl($this->favicon_path);
    }

    public function getFooterLogoUrlAttribute(): ?string
    {
        if (! $this->footer_logo_path) return null;
        return $this->makeStorageUrl($this->footer_logo_path);
    }

    public function getAboutImageUrlAttribute(): ?string
    {
        if (! $this->about_image) return null;
        return $this->makeStorageUrl($this->about_image);
    }

    public function getHomeAboutImageUrlAttribute(): ?string
    {
        if (! $this->home_about_image) return null;
        return $this->makeStorageUrl($this->home_about_image);
    }

    private function makeStorageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return Storage::disk('public')->url($path);
    }
}
