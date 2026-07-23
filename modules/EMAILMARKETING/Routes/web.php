<?php

use Illuminate\Support\Facades\Route;
use Modules\EMAILMARKETING\Http\Controllers\EmailCampaignController;
use Modules\EMAILMARKETING\Http\Controllers\EmailMarketingController;
use Modules\EMAILMARKETING\Http\Controllers\EmailTemplateController;
use Modules\EMAILMARKETING\Http\Controllers\EmailTrackingController;
use Modules\EMAILMARKETING\Http\Controllers\NewsletterController;

Route::middleware('web')->group(function (): void {
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('/newsletter/unsubscribe/{newsletter}/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

    Route::get('/email-marketing/open/{recipient}/{token}', [EmailTrackingController::class, 'open'])->name('emailmarketing.track.open');
    Route::get('/email-marketing/click/{recipient}/{token}', [EmailTrackingController::class, 'click'])->name('emailmarketing.track.click');
});

Route::middleware(['web', 'auth'])->prefix('email-marketing')->name('emailmarketing.')->group(function (): void {
    Route::get('/', [EmailMarketingController::class, 'index'])->name('index');

    Route::post('/subscribers', [NewsletterController::class, 'store'])->name('subscribers.store');
    Route::put('/subscribers/{newsletter}', [NewsletterController::class, 'update'])->name('subscribers.update');
    Route::delete('/subscribers/{newsletter}', [NewsletterController::class, 'destroy'])->name('subscribers.destroy');
    Route::post('/subscribers/import', [NewsletterController::class, 'import'])->name('subscribers.import');

    Route::post('/templates', [EmailTemplateController::class, 'store'])->name('templates.store');
    Route::put('/templates/{template}', [EmailTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [EmailTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('templates.duplicate');
    Route::post('/templates/{template}/test', [EmailTemplateController::class, 'test'])->name('templates.test');

    Route::post('/campaigns', [EmailCampaignController::class, 'store'])->name('campaigns.store');
    Route::put('/campaigns/{campaign}', [EmailCampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [EmailCampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::post('/campaigns/{campaign}/send', [EmailCampaignController::class, 'send'])->name('campaigns.send');
    Route::post('/campaigns/{campaign}/schedule', [EmailCampaignController::class, 'schedule'])->name('campaigns.schedule');
    Route::post('/campaigns/{campaign}/duplicate', [EmailCampaignController::class, 'duplicate'])->name('campaigns.duplicate');
    Route::post('/campaigns/{campaign}/cancel', [EmailCampaignController::class, 'cancel'])->name('campaigns.cancel');
    Route::post('/campaigns/{campaign}/test', [EmailCampaignController::class, 'test'])->name('campaigns.test');
});
