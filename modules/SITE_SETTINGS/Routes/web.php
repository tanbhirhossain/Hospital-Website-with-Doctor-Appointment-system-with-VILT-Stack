<?php

use Illuminate\Support\Facades\Route;
use Modules\SITE_SETTINGS\Http\Controllers\SiteSettingsController;
use Modules\SITE_SETTINGS\Http\Controllers\NavigationMenuController;
use Modules\SITE_SETTINGS\Http\Controllers\QuickCardController;
use Modules\SITE_SETTINGS\Http\Controllers\CorporatePartnerController;
use Modules\SITE_SETTINGS\Http\Controllers\WhyChooseUsController;
use Modules\SITE_SETTINGS\Http\Controllers\HomeVideoController;
use Modules\SITE_SETTINGS\Http\Controllers\HomeSectionOrderController;

Route::middleware(['web', 'auth'])->name('admin.')->prefix('admin')->group(function (): void {

    // ─── General Site Settings (tabbed pages) ───────────────────────
    Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('site-settings.index');
    Route::put('site-settings/topbar', [SiteSettingsController::class, 'updateTopbar'])->name('site-settings.topbar');
    Route::put('site-settings/logo', [SiteSettingsController::class, 'updateLogo'])->name('site-settings.logo');
    Route::put('site-settings/about-page', [SiteSettingsController::class, 'updateAboutPage'])->name('site-settings.about-page');
    Route::put('site-settings/contact-page', [SiteSettingsController::class, 'updateContactPage'])->name('site-settings.contact-page');
    Route::put('site-settings/contact-info', [SiteSettingsController::class, 'updateContactInfo'])->name('site-settings.contact-info');
    Route::put('site-settings/footer', [SiteSettingsController::class, 'updateFooter'])->name('site-settings.footer');
    Route::put('site-settings/home-about', [SiteSettingsController::class, 'updateHomeAbout'])->name('site-settings.home-about');
    Route::put('site-settings/why-choose-section', [SiteSettingsController::class, 'updateWhyChooseSection'])->name('site-settings.why-choose-section');
    Route::put('site-settings/services-section', [SiteSettingsController::class, 'updateServicesSection'])->name('site-settings.services-section');
    Route::put('site-settings/partners-section', [SiteSettingsController::class, 'updatePartnersSection'])->name('site-settings.partners-section');

    // ─── Navigation Menus ──────────────────────────────────────────
    Route::get('navigation-menus', [NavigationMenuController::class, 'index'])->name('navigation.index');
    Route::post('navigation-menus', [NavigationMenuController::class, 'store'])->name('navigation.store');
    Route::put('navigation-menus/{navigationMenu}', [NavigationMenuController::class, 'update'])->name('navigation.update');
    Route::delete('navigation-menus/{navigationMenu}', [NavigationMenuController::class, 'destroy'])->name('navigation.destroy');
    Route::post('navigation-menus/merge', [NavigationMenuController::class, 'merge'])->name('navigation.merge');
    Route::post('navigation-menus/reorder', [NavigationMenuController::class, 'reorder'])->name('navigation.reorder');

    // ─── Quick Cards ───────────────────────────────────────────────
    Route::get('quick-cards', [QuickCardController::class, 'index'])->name('quick-cards.index');
    Route::post('quick-cards', [QuickCardController::class, 'store'])->name('quick-cards.store');
    Route::put('quick-cards/{quickCard}', [QuickCardController::class, 'update'])->name('quick-cards.update');
    Route::delete('quick-cards/{quickCard}', [QuickCardController::class, 'destroy'])->name('quick-cards.destroy');

    // ─── Corporate Partners ────────────────────────────────────────
    Route::get('corporate-partners', [CorporatePartnerController::class, 'index'])->name('corporate-partners.index');
    Route::post('corporate-partners', [CorporatePartnerController::class, 'store'])->name('corporate-partners.store');
    Route::put('corporate-partners/{corporatePartner}', [CorporatePartnerController::class, 'update'])->name('corporate-partners.update');
    Route::delete('corporate-partners/{corporatePartner}', [CorporatePartnerController::class, 'destroy'])->name('corporate-partners.destroy');

    // ─── Why Choose Us Items ───────────────────────────────────────
    Route::get('why-choose-us', [WhyChooseUsController::class, 'index'])->name('why-choose-us.index');
    Route::post('why-choose-us', [WhyChooseUsController::class, 'store'])->name('why-choose-us.store');
    Route::put('why-choose-us/{whyChooseUsItem}', [WhyChooseUsController::class, 'update'])->name('why-choose-us.update');
    Route::delete('why-choose-us/{whyChooseUsItem}', [WhyChooseUsController::class, 'destroy'])->name('why-choose-us.destroy');

    // ─── Home Videos ───────────────────────────────────────────────
    Route::get('home-videos', [HomeVideoController::class, 'index'])->name('home-videos.index');
    Route::post('home-videos', [HomeVideoController::class, 'store'])->name('home-videos.store');
    Route::put('home-videos/{homeVideo}', [HomeVideoController::class, 'update'])->name('home-videos.update');
    Route::delete('home-videos/{homeVideo}', [HomeVideoController::class, 'destroy'])->name('home-videos.destroy');

    // ─── Home Section Order ────────────────────────────────────────
    Route::get('home-sections', [HomeSectionOrderController::class, 'index'])->name('home-sections.index');
    Route::put('home-sections/order', [HomeSectionOrderController::class, 'update'])->name('home-sections.update');
});
