<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_marketing_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('email_campaign_id')->nullable()->constrained('email_campaigns')->cascadeOnDelete();
            $table->foreignId('email_campaign_recipient_id')->nullable()->constrained('email_campaign_recipients')->cascadeOnDelete();
            $table->foreignId('newsletter_id')->nullable()->constrained('newsletters')->nullOnDelete();
            $table->string('email')->nullable()->index();
            $table->string('type')->index();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['email_campaign_id', 'type']);
            $table->index(['email_campaign_recipient_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_marketing_events');
    }
};
