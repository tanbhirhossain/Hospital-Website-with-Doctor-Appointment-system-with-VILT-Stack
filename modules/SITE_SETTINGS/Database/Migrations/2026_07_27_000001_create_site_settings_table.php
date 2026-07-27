<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            // Topbar
            $table->string('topbar_phone')->nullable();
            $table->string('topbar_email')->nullable();
            $table->string('topbar_notice')->nullable();
            $table->string('topbar_emergency')->nullable();

            // Logo
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('footer_logo_path')->nullable();

            // About Us Page
            $table->string('about_title')->nullable();
            $table->text('about_content')->nullable();
            $table->string('about_image')->nullable();
            $table->string('about_video_url')->nullable();
            $table->string('about_video_title')->nullable();

            // Contact Us Page
            $table->string('contact_title')->nullable();
            $table->text('contact_content')->nullable();
            $table->string('contact_map_embed')->nullable();

            // Contact Information
            $table->string('contact_phone_primary')->nullable();
            $table->string('contact_phone_secondary')->nullable();
            $table->string('contact_email_primary')->nullable();
            $table->string('contact_email_secondary')->nullable();
            $table->string('contact_hotline')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_city')->nullable();

            // Footer
            $table->text('footer_description')->nullable();
            $table->string('footer_phone')->nullable();
            $table->string('footer_email')->nullable();
            $table->string('footer_address')->nullable();
            $table->string('footer_facebook')->nullable();
            $table->string('footer_twitter')->nullable();
            $table->string('footer_linkedin')->nullable();
            $table->string('footer_youtube')->nullable();
            $table->string('footer_instagram')->nullable();
            $table->text('footer_copyright')->nullable();

            // Home: Quick Card section title
            $table->string('quick_cards_title')->nullable();

            // Home: About Us section
            $table->string('home_about_title')->nullable();
            $table->text('home_about_content')->nullable();
            $table->string('home_about_video_url')->nullable();
            $table->string('home_about_image')->nullable();

            // Home: Why Choose Us section
            $table->string('why_choose_title')->nullable();
            $table->text('why_choose_subtitle')->nullable();

            // Home: Healthcare Services section
            $table->string('services_title')->nullable();
            $table->text('services_subtitle')->nullable();

            // Home: Corporate Partners section
            $table->string('partners_title')->nullable();
            $table->text('partners_subtitle')->nullable();

            // Section order (JSON array of section keys)
            $table->json('section_order')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
