<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\SiteSetting;

class HomeSectionOrderController extends Controller
{
    /**
     * Available sections for the homepage.
     */
    private const AVAILABLE_SECTIONS = [
        'hero_slider'           => ['label' => 'Hero Slider',           'icon' => 'Image'],
        'quick_cards'           => ['label' => 'Quick Access Cards',    'icon' => 'LayoutGrid'],
        'about_us'              => ['label' => 'About Us Content',      'icon' => 'Info'],
        'about_videos'          => ['label' => 'About Us Videos',       'icon' => 'Video'],
        'why_choose_us'         => ['label' => 'Why Choose Us',         'icon' => 'Award'],
        'departments'           => ['label' => 'Departments',           'icon' => 'Building2'],
        'centers_of_excellence' => ['label' => 'Centers of Excellence', 'icon' => 'Star'],
        'healthcare_services'   => ['label' => 'Healthcare Services',   'icon' => 'HeartPulse'],
        'doctors'               => ['label' => 'Doctors',               'icon' => 'Users'],
        'leadership'            => ['label' => 'Leadership',            'icon' => 'Crown'],
        'gallery'               => ['label' => 'Gallery',               'icon' => 'ImageIcon'],
        'health_packages'       => ['label' => 'Health Packages',       'icon' => 'Package'],
        'appointment'           => ['label' => 'Appointment Booking',   'icon' => 'Calendar'],
        'testimonials'          => ['label' => 'Testimonials',          'icon' => 'Quote'],
        'stats'                 => ['label' => 'Stats Counter',         'icon' => 'BarChart3'],
        'blog'                  => ['label' => 'Blog Posts',            'icon' => 'Newspaper'],
        'corporate_partners'    => ['label' => 'Corporate Partners',    'icon' => 'Handshake'],
        'contact'               => ['label' => 'Contact Section',       'icon' => 'MapPin'],
    ];

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('site-settings.view') || $request->user()?->hasRole('super admin'), 403);

        $settings = SiteSetting::current();
        $currentOrder = $settings->section_order ?? [];

        // Build sections list: ordered first, then any new ones not yet in order
        $sections = [];
        foreach ($currentOrder as $key) {
            if (isset(self::AVAILABLE_SECTIONS[$key])) {
                $sections[] = array_merge(['key' => $key], self::AVAILABLE_SECTIONS[$key]);
            }
        }
        foreach (self::AVAILABLE_SECTIONS as $key => $meta) {
            if (!in_array($key, $currentOrder)) {
                $sections[] = array_merge(['key' => $key], $meta);
            }
        }

        return Inertia::render('SITE_SETTINGS::HomeSections/Index', [
            'sections'       => $sections,
            'currentOrder'   => $currentOrder,
            'availableSections' => self::AVAILABLE_SECTIONS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'order'   => 'required|array',
            'order.*' => 'string',
        ]);

        // Validate that all items are valid section keys
        $validKeys = array_keys(self::AVAILABLE_SECTIONS);
        foreach ($data['order'] as $key) {
            if (!in_array($key, $validKeys)) {
                return back()->with('error', "Invalid section key: {$key}");
            }
        }

        SiteSetting::current()->update([
            'section_order' => json_encode($data['order']),
        ]);

        return back()->with('success', 'Homepage section order updated successfully.');
    }
}
