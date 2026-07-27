<?php

namespace Modules\SITE_SETTINGS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Modules\SITE_SETTINGS\Models\SiteSetting;

class SiteSettingsController extends Controller
{
    /**
     * Show the main settings page with tabs.
     */
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('site-settings.view') || $request->user()?->hasRole('super admin'), 403);

        $settings = SiteSetting::current();

        return Inertia::render('SITE_SETTINGS::SiteSettings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update topbar settings.
     */
    public function updateTopbar(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'topbar_phone'     => 'nullable|string|max:50',
            'topbar_email'     => 'nullable|email|max:100',
            'topbar_notice'    => 'nullable|string|max:255',
            'topbar_emergency' => 'nullable|string|max:50',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Topbar info updated successfully.');
    }

    /**
     * Update logo settings.
     */
    public function updateLogo(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $settings = SiteSetting::current();
        $data = $request->validate([
            'logo'         => 'nullable|image|max:2048',
            'favicon'      => 'nullable|image|max:1024',
            'footer_logo'  => 'nullable|image|max:2048',
        ]);

        $updateData = [];

        // Logo
        if ($request->hasFile('logo')) {
            if ($settings->logo_path && !str_starts_with($settings->logo_path, 'http')) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $updateData['logo_path'] = $request->file('logo')->store('site-settings/logos', 'public');
        }

        // Favicon
        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path && !str_starts_with($settings->favicon_path, 'http')) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $updateData['favicon_path'] = $request->file('favicon')->store('site-settings/favicons', 'public');
        }

        // Footer Logo
        if ($request->hasFile('footer_logo')) {
            if ($settings->footer_logo_path && !str_starts_with($settings->footer_logo_path, 'http')) {
                Storage::disk('public')->delete($settings->footer_logo_path);
            }
            $updateData['footer_logo_path'] = $request->file('footer_logo')->store('site-settings/logos', 'public');
        }

        $settings->update($updateData);

        return back()->with('success', 'Logo updated successfully.');
    }

    /**
     * Update About Us page content.
     */
    public function updateAboutPage(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $settings = SiteSetting::current();

        $data = $request->validate([
            'about_title'       => 'nullable|string|max:255',
            'about_content'     => 'nullable|string',
            'about_video_url'   => 'nullable|string|max:500',
            'about_video_title' => 'nullable|string|max:255',
            'about_image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('about_image')) {
            if ($settings->about_image && !str_starts_with($settings->about_image, 'http')) {
                Storage::disk('public')->delete($settings->about_image);
            }
            $data['about_image'] = $request->file('about_image')->store('site-settings/about', 'public');
        } else {
            unset($data['about_image']);
        }

        $settings->update($data);

        return back()->with('success', 'About Us page content updated successfully.');
    }

    /**
     * Update Contact Us page content.
     */
    public function updateContactPage(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'contact_title'   => 'nullable|string|max:255',
            'contact_content' => 'nullable|string',
            'contact_map_embed' => 'nullable|string',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Contact Us page content updated successfully.');
    }

    /**
     * Update Contact Information.
     */
    public function updateContactInfo(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'contact_phone_primary'   => 'nullable|string|max:50',
            'contact_phone_secondary' => 'nullable|string|max:50',
            'contact_email_primary'   => 'nullable|email|max:100',
            'contact_email_secondary' => 'nullable|email|max:100',
            'contact_hotline'         => 'nullable|string|max:50',
            'contact_address'         => 'nullable|string|max:500',
            'contact_city'            => 'nullable|string|max:100',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Contact information updated successfully.');
    }

    /**
     * Update Footer information.
     */
    public function updateFooter(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'footer_description' => 'nullable|string',
            'footer_phone'       => 'nullable|string|max:50',
            'footer_email'       => 'nullable|email|max:100',
            'footer_address'     => 'nullable|string|max:500',
            'footer_facebook'    => 'nullable|url|max:255',
            'footer_twitter'     => 'nullable|url|max:255',
            'footer_linkedin'    => 'nullable|url|max:255',
            'footer_youtube'     => 'nullable|url|max:255',
            'footer_instagram'   => 'nullable|url|max:255',
            'footer_copyright'   => 'nullable|string|max:500',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Footer information updated successfully.');
    }

    /**
     * Update Home page About Us section content.
     */
    public function updateHomeAbout(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $settings = SiteSetting::current();

        $data = $request->validate([
            'home_about_title'     => 'nullable|string|max:255',
            'home_about_content'   => 'nullable|string',
            'home_about_video_url' => 'nullable|string|max:500',
            'home_about_image'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('home_about_image')) {
            if ($settings->home_about_image && !str_starts_with($settings->home_about_image, 'http')) {
                Storage::disk('public')->delete($settings->home_about_image);
            }
            $data['home_about_image'] = $request->file('home_about_image')->store('site-settings/home-about', 'public');
        } else {
            unset($data['home_about_image']);
        }

        $settings->update($data);

        return back()->with('success', 'Home About Us section updated successfully.');
    }

    /**
     * Update Why Choose Us section title/subtitle.
     */
    public function updateWhyChooseSection(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'why_choose_title'    => 'nullable|string|max:255',
            'why_choose_subtitle' => 'nullable|string|max:500',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Why Choose Us section updated successfully.');
    }

    /**
     * Update Healthcare Services section title/subtitle.
     */
    public function updateServicesSection(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'services_title'    => 'nullable|string|max:255',
            'services_subtitle' => 'nullable|string|max:500',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Healthcare Services section updated successfully.');
    }

    /**
     * Update Corporate Partners section title/subtitle.
     */
    public function updatePartnersSection(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('site-settings.edit') || $request->user()?->hasRole('super admin'), 403);

        $data = $request->validate([
            'partners_title'    => 'nullable|string|max:255',
            'partners_subtitle' => 'nullable|string|max:500',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'Corporate Partners section updated successfully.');
    }
}
