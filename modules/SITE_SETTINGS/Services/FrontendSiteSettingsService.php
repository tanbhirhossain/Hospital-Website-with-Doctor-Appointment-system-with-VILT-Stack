<?php

namespace Modules\SITE_SETTINGS\Services;

use Modules\SITE_SETTINGS\Models\SiteSetting;
use Modules\SITE_SETTINGS\Models\QuickCard;
use Modules\SITE_SETTINGS\Models\CorporatePartner;
use Modules\SITE_SETTINGS\Models\WhyChooseUsItem;
use Modules\SITE_SETTINGS\Models\NavigationMenu;
use Modules\SITE_SETTINGS\Models\HomeVideo;

class FrontendSiteSettingsService
{
    /**
     * Get all data needed for the frontend homepage.
     */
    public function homepageData(): array
    {
        $settings = SiteSetting::current();

        return [
            'site_settings' => [
                'topbar' => [
                    'phone'     => $settings->topbar_phone,
                    'email'     => $settings->topbar_email,
                    'notice'    => $settings->topbar_notice,
                    'emergency' => $settings->topbar_emergency,
                ],
                'logo' => [
                    'logo_url'        => $settings->logo_url,
                    'favicon_url'     => $settings->favicon_url,
                    'footer_logo_url' => $settings->footer_logo_url,
                ],
                'footer' => [
                    'description' => $settings->footer_description,
                    'phone'       => $settings->footer_phone,
                    'email'       => $settings->footer_email,
                    'address'     => $settings->footer_address,
                    'facebook'    => $settings->footer_facebook,
                    'twitter'     => $settings->footer_twitter,
                    'linkedin'    => $settings->footer_linkedin,
                    'youtube'     => $settings->footer_youtube,
                    'instagram'   => $settings->footer_instagram,
                    'copyright'   => $settings->footer_copyright,
                ],
                'contact' => [
                    'phone_primary'   => $settings->contact_phone_primary,
                    'phone_secondary' => $settings->contact_phone_secondary,
                    'email_primary'   => $settings->contact_email_primary,
                    'email_secondary' => $settings->contact_email_secondary,
                    'hotline'         => $settings->contact_hotline,
                    'address'         => $settings->contact_address,
                    'city'            => $settings->contact_city,
                ],
                'contact_page' => [
                    'title'       => $settings->contact_title,
                    'content'     => $settings->contact_content,
                    'map_embed'   => $settings->contact_map_embed,
                ],
                'about_page' => [
                    'title'          => $settings->about_title,
                    'content'        => $settings->about_content,
                    'image_url'      => $settings->about_image_url,
                    'video_url'      => $settings->about_video_url,
                    'video_title'    => $settings->about_video_title,
                ],
                'home_about' => [
                    'title'     => $settings->home_about_title,
                    'content'   => $settings->home_about_content,
                    'video_url' => $settings->home_about_video_url,
                    'image_url' => $settings->home_about_image_url,
                ],
                'why_choose' => [
                    'title'    => $settings->why_choose_title,
                    'subtitle' => $settings->why_choose_subtitle,
                ],
                'services' => [
                    'title'    => $settings->services_title,
                    'subtitle' => $settings->services_subtitle,
                ],
                'partners' => [
                    'title'    => $settings->partners_title,
                    'subtitle' => $settings->partners_subtitle,
                ],
                'section_order' => $settings->section_order ?? [],
            ],

            'quick_cards' => QuickCard::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($card) => [
                    'id'       => $card->id,
                    'title'    => $card->title,
                    'link'     => $card->link,
                    'icon'     => $card->icon,
                    'gradient' => $card->gradient,
                ]),

            'why_choose_items' => WhyChooseUsItem::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($item) => [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'description' => $item->description,
                    'icon'        => $item->icon,
                    'gradient'    => $item->gradient,
                    'color'       => $item->color,
                ]),

            'corporate_partners' => CorporatePartner::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($p) => [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'logo_url'    => $p->logo_url,
                    'website_url' => $p->website_url,
                ]),

            'home_videos' => HomeVideo::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($v) => [
                    'id'            => $v->id,
                    'title'         => $v->title,
                    'video_url'     => $v->video_url,
                    'video_type'    => $v->video_type,
                    'thumbnail_url' => $v->thumbnail_url,
                    'description'   => $v->description,
                ]),

            'navigation_menus' => NavigationMenu::tree('header'),
        ];
    }

    /**
     * Get shared data for all frontend pages (topbar, footer, navigation, logos).
     */
    public function sharedFrontendData(): array
    {
        $settings = SiteSetting::current();

        return [
            'site_settings' => [
                'topbar' => [
                    'phone'     => $settings->topbar_phone,
                    'email'     => $settings->topbar_email,
                    'notice'    => $settings->topbar_notice,
                    'emergency' => $settings->topbar_emergency,
                ],
                'logo' => [
                    'logo_url'        => $settings->logo_url,
                    'favicon_url'     => $settings->favicon_url,
                    'footer_logo_url' => $settings->footer_logo_url,
                ],
                'footer' => [
                    'description' => $settings->footer_description,
                    'phone'       => $settings->footer_phone,
                    'email'       => $settings->footer_email,
                    'address'     => $settings->footer_address,
                    'facebook'    => $settings->footer_facebook,
                    'twitter'     => $settings->footer_twitter,
                    'linkedin'    => $settings->footer_linkedin,
                    'youtube'     => $settings->footer_youtube,
                    'instagram'   => $settings->footer_instagram,
                    'copyright'   => $settings->footer_copyright,
                ],
                'contact' => [
                    'phone_primary'   => $settings->contact_phone_primary,
                    'phone_secondary' => $settings->contact_phone_secondary,
                    'email_primary'   => $settings->contact_email_primary,
                    'email_secondary' => $settings->contact_email_secondary,
                    'hotline'         => $settings->contact_hotline,
                    'address'         => $settings->contact_address,
                    'city'            => $settings->contact_city,
                ],
                'navigation_menus' => NavigationMenu::tree('header'),
            ],
        ];
    }

    /**
     * Get about page specific data.
     */
    public function aboutPageData(): array
    {
        $settings = SiteSetting::current();

        return [
            'about_page' => [
                'title'       => $settings->about_title,
                'content'     => $settings->about_content,
                'image_url'   => $settings->about_image_url,
                'video_url'   => $settings->about_video_url,
                'video_title' => $settings->about_video_title,
            ],
        ];
    }

    /**
     * Get contact page specific data.
     */
    public function contactPageData(): array
    {
        $settings = SiteSetting::current();

        return [
            'contact_page' => [
                'title'   => $settings->contact_title,
                'content' => $settings->contact_content,
                'map_embed' => $settings->contact_map_embed,
            ],
            'contact_info' => [
                'phone_primary'   => $settings->contact_phone_primary,
                'phone_secondary' => $settings->contact_phone_secondary,
                'email_primary'   => $settings->contact_email_primary,
                'email_secondary' => $settings->contact_email_secondary,
                'hotline'         => $settings->contact_hotline,
                'address'         => $settings->contact_address,
                'city'            => $settings->contact_city,
            ],
        ];
    }
}
