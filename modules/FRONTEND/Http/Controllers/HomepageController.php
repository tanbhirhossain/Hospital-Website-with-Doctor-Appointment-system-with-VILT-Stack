<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\BLOG\Services\BlogService;
use Modules\GALLERY\Interfaces\GalleryItemRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\HeroSliderRepositoryInterface;
use Modules\WEBSITE_EXTRA\Interfaces\PatientReviewRepositoryInterface;
use Modules\WEBSITE\Interfaces\COERepositoryInterface;
use Modules\WEBSITE\Interfaces\DepartmentRepositoryInterface;
use Modules\WEBSITE\Interfaces\DoctorRepositoryInterface;
use Modules\SITE_SETTINGS\Services\FrontendSiteSettingsService;
use Modules\WEBSITE_EXTRA\Models\LeadershipMessage;

class HomepageController extends Controller
{
    public function __construct(
        private readonly DoctorRepositoryInterface $drRepo,
        private readonly DepartmentRepositoryInterface $deptRepo,
        private readonly COERepositoryInterface $coeRepo,
        private readonly BlogService $blogService,
        private readonly HeroSliderRepositoryInterface $slideRepo,
        private readonly GalleryItemRepositoryInterface $galleryRepo,
        private readonly PatientReviewRepositoryInterface $patientReviewRepo,
        private readonly FrontendSiteSettingsService $siteSettingsService,
    ) {}

    public function index()
    {
        $slides = $this->slideRepo->allforHomepage()->map(function ($slide) {
            if (is_string($slide->buttons)) {
                $cleanJson = str_replace(["\r\n", "\r", "\n"], '', $slide->buttons);
                $slide->buttons = json_decode(stripslashes($cleanJson), true);
            }
            return $slide;
        });

        $reviews = $this->patientReviewRepo->listForHomepage()->map(function ($review) {
            $text = $review->review_text ?? '';
            $isLong = Str::length($text) > 100;

            // Generate strict 2-character initials dynamically if database column is null/empty
            $initials = $review->initials;
            if (!$initials && $review->patient_name) {
                $words = explode(' ', trim($review->patient_name));
                $initials = count($words) >= 2 
                    ? mb_substr($words[0], 0, 1) . mb_substr($words[count($words) - 1], 0, 1)
                    : mb_substr($words[0], 0, 2);
            }
            $initials = strtoupper(mb_substr($initials ?: 'PA', 0, 2));

            return [
                'initials' => $initials,
                'bg' => $review->bg ?? 'from-blue-800 to-sky-500',
                'cardBg' => $review->cardBg ?? 'from-blue-50',
                'name' => $review->patient_name ?? 'Anonymous',
                'short' => $isLong ? Str::substr($text, 0, 100) . '...' : $text,
                'full' => $isLong ? Str::substr($text, 100) : '',
            ];
        });
        // dd($reviews);

        $siteData = $this->siteSettingsService->homepageData();

        $leadership = LeadershipMessage::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => [
                'name'        => $m->name,
                'role'        => $m->role,
                'roleLine'    => $m->role_line,
                'eyebrow'     => $m->eyebrow,
                'title'       => $m->title,
                'quote'       => $m->quote,
                'credentials' => $m->credentials ?? [],
                'photo'       => $m->photo_url,
                'message'     => $m->message,
            ]);

        return Inertia::render('FRONTEND::Home', [
            'doctors' => $this->drRepo->allHomePageDoctor(),
            'departments' => $this->deptRepo->list_for_home_page(),
            'centers' => $this->coeRepo->listForHome(),
            'blogs' => $this->blogService->latestThree(),
            'slides' => $slides,
            'galleries' => $this->galleryRepo->allforHome(),
            'reviews' => $reviews,
            // Site Settings data
            'site_settings'   => $siteData['site_settings'],
            'quick_cards'     => $siteData['quick_cards'],
            'why_choose_items' => $siteData['why_choose_items'],
            'corporate_partners' => $siteData['corporate_partners'],
            'home_videos'     => $siteData['home_videos'],
            'navigation_menus' => $siteData['navigation_menus'],
            'leadership'      => $leadership,
        ]);
    }
}